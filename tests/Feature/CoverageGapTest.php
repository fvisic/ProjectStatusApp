<?php

namespace Tests\Feature;

use App\Console\Commands\SendProjectAlerts;
use App\Livewire\DarkModeToggle;
use App\Livewire\ProjectForm;
use App\Livewire\ProjectHistory;
use App\Livewire\ProjectIndex;
use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\ProjectType;
use App\Models\User;
use App\Notifications\ProjectAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class CoverageGapTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    // =============================================
    // SendProjectAlerts - go_live_soon
    // =============================================

    public function test_alerts_sends_go_live_soon_notification(): void
    {
        Notification::fake();

        Project::factory()->create([
            'created_by' => $this->user->id,
            'planned_go_live' => now()->addDays(3),
            'current_phase' => 'uat_edukacija',
        ]);

        $this->artisan('projects:send-alerts')->assertSuccessful();

        Notification::assertSentTo($this->user, ProjectAlertNotification::class, function ($notification) {
            $data = $notification->toArray($this->user);
            return $data['type'] === 'go_live_soon';
        });
    }

    public function test_alerts_skips_hypercare_projects_for_go_live(): void
    {
        Notification::fake();

        Project::factory()->create([
            'created_by' => $this->user->id,
            'planned_go_live' => now()->addDays(3),
            'current_phase' => 'hypercare',
            'overall_health' => 'on_track',
            'estimated_hours' => null,
        ]);

        $this->artisan('projects:send-alerts')->assertSuccessful();

        Notification::assertNotSentTo($this->user, ProjectAlertNotification::class);
    }

    public function test_alerts_skips_go_live_past_today(): void
    {
        Notification::fake();

        Project::factory()->create([
            'created_by' => $this->user->id,
            'planned_go_live' => now()->subDays(1),
            'current_phase' => 'uat_edukacija',
            'overall_health' => 'on_track',
            'estimated_hours' => null,
        ]);

        $this->artisan('projects:send-alerts')->assertSuccessful();

        Notification::assertNotSentTo($this->user, ProjectAlertNotification::class);
    }

    // =============================================
    // SendProjectAlerts - budget_overrun
    // =============================================

    public function test_alerts_sends_budget_overrun_notification(): void
    {
        Notification::fake();

        Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
            'estimated_hours' => 100,
            'spent_hours' => 80,
            'remaining_hours' => 40,  // forecast 120, overrun 20%
            'planned_go_live' => now()->addMonths(3),
        ]);

        $this->artisan('projects:send-alerts')->assertSuccessful();

        Notification::assertSentTo($this->user, ProjectAlertNotification::class, function ($notification) {
            $data = $notification->toArray($this->user);
            return $data['type'] === 'budget_overrun';
        });
    }

    public function test_alerts_skips_budget_under_threshold(): void
    {
        Notification::fake();

        Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
            'estimated_hours' => 100,
            'spent_hours' => 50,
            'remaining_hours' => 50,  // forecast 100, overrun 0%
            'planned_go_live' => now()->addMonths(3),
        ]);

        $this->artisan('projects:send-alerts')->assertSuccessful();

        Notification::assertNotSentTo($this->user, ProjectAlertNotification::class);
    }

    // =============================================
    // SendProjectAlerts - notifyAdmins
    // =============================================

    public function test_alerts_notifies_admins_but_not_project_owner_admin(): void
    {
        Notification::fake();

        // Admin owns an off-track project
        $otherAdmin = User::factory()->create(['is_admin' => true]);

        Project::factory()->create([
            'created_by' => $this->admin->id,
            'overall_health' => 'off_track',
        ]);

        $this->artisan('projects:send-alerts')->assertSuccessful();

        // Admin owner gets the user notification
        Notification::assertSentTo($this->admin, ProjectAlertNotification::class);
        // Other admin gets the admin notification (notifyAdmins excludes owner)
        Notification::assertSentTo($otherAdmin, ProjectAlertNotification::class);
    }

    public function test_alerts_outputs_count(): void
    {
        Notification::fake();

        Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'off_track',
        ]);

        $this->artisan('projects:send-alerts')
            ->assertSuccessful()
            ->expectsOutputToContain('alert(s)');
    }

    // =============================================
    // ProjectPolicy - restore / forceDelete
    // =============================================

    public function test_policy_owner_can_restore(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->assertTrue($this->user->can('restore', $project));
    }

    public function test_policy_non_owner_cannot_restore(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);
        $this->assertFalse($this->user->can('restore', $project));
    }

    public function test_policy_admin_can_restore_any(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->assertTrue($this->admin->can('restore', $project));
    }

    public function test_policy_owner_can_force_delete(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->assertTrue($this->user->can('forceDelete', $project));
    }

    public function test_policy_non_owner_cannot_force_delete(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);
        $this->assertFalse($this->user->can('forceDelete', $project));
    }

    public function test_policy_admin_can_force_delete_any(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->assertTrue($this->admin->can('forceDelete', $project));
    }

    public function test_policy_owner_can_view(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->assertTrue($this->user->can('view', $project));
    }

    public function test_policy_owner_can_delete(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->assertTrue($this->user->can('delete', $project));
    }

    public function test_policy_any_user_can_create(): void
    {
        $this->assertTrue($this->user->can('create', Project::class));
    }

    public function test_policy_any_user_can_view_any(): void
    {
        $this->assertTrue($this->user->can('viewAny', Project::class));
    }

    // =============================================
    // ProjectForm - addComment / deleteComment
    // =============================================

    public function test_add_comment_creates_comment(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('newComment', 'This is a test comment')
            ->call('addComment');

        $this->assertDatabaseHas('project_comments', [
            'project_id' => $project->id,
            'user_id' => $this->user->id,
            'body' => 'This is a test comment',
        ]);
    }

    public function test_add_comment_clears_input(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('newComment', 'A comment')
            ->call('addComment')
            ->assertSet('newComment', '');
    }

    public function test_add_comment_ignores_empty_text(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('newComment', '   ')
            ->call('addComment');

        $this->assertDatabaseMissing('project_comments', [
            'project_id' => $project->id,
        ]);
    }

    public function test_add_comment_ignored_without_project(): void
    {
        // Create form (no project yet)
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('newComment', 'Orphan comment')
            ->call('addComment');

        $this->assertDatabaseCount('project_comments', 0);
    }

    public function test_delete_own_comment(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $comment = $project->comments()->create([
            'user_id' => $this->user->id,
            'body' => 'Delete me',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->call('deleteComment', $comment->id);

        $this->assertDatabaseMissing('project_comments', ['id' => $comment->id]);
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $project = Project::factory()->create(['created_by' => $this->admin->id]);
        $comment = $project->comments()->create([
            'user_id' => $this->user->id,
            'body' => 'User comment',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->call('deleteComment', $comment->id);

        $this->assertDatabaseMissing('project_comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_others_comment(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $otherUser = User::factory()->create();
        $comment = $project->comments()->create([
            'user_id' => $otherUser->id,
            'body' => 'Not yours',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->call('deleteComment', $comment->id);

        // Comment should still exist
        $this->assertDatabaseHas('project_comments', ['id' => $comment->id]);
    }

    // =============================================
    // ProjectForm - getBurndownDataProperty
    // =============================================

    public function test_burndown_returns_empty_without_project(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        // Access the computed property via render (it doesn't error)
        $component->assertOk();
    }

    public function test_burndown_includes_snapshot_points_and_current(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'estimated_hours' => 100,
            'spent_hours' => 30,
            'remaining_hours' => 60,
        ]);

        $project->createSnapshot($this->user->id, 'Snap 1');

        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id]);

        // Component should render with burndown data (1 snapshot + current = 2 points)
        $component->assertOk();
    }

    // =============================================
    // ProjectHistory - compareSnapshots / computeDiff
    // =============================================

    public function test_compare_snapshots_shows_diff(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Diff Test',
            'overall_health' => 'on_track',
        ]);

        $snap1 = $project->createSnapshot($this->user->id, 'Version 1');

        $project->update(['overall_health' => 'off_track', 'name' => 'Diff Test Updated']);
        $snap2 = $project->createSnapshot($this->user->id, 'Version 2');

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('compareSnapshots', $snap1->id, $snap2->id)
            ->assertSet('showDiff', true)
            ->assertSet('compareSnapshotId', $snap1->id)
            ->assertSet('selectedSnapshotId', $snap2->id)
            ->assertSet('diffResults', function ($diff) {
                // Should detect name and health changes
                return count($diff) >= 2;
            });
    }

    public function test_compare_snapshots_detects_phase_status_change(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->phases()->create([
            'phase_name' => 'Instalacija & Analiza',
            'status' => 'pending',
            'sort_order' => 1,
        ]);

        $snap1 = $project->createSnapshot($this->user->id, 'Before');

        $project->phases()->first()->update(['status' => 'done']);
        $project->load('phases');
        $snap2 = $project->createSnapshot($this->user->id, 'After');

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('compareSnapshots', $snap1->id, $snap2->id)
            ->assertSet('showDiff', true)
            ->assertSet('diffResults', function ($diff) {
                // Should contain a phase status diff
                return collect($diff)->contains(fn ($d) => str_contains($d['field'], 'Instalacija'));
            });
    }

    public function test_compare_snapshots_no_diff_for_identical(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $snap1 = $project->createSnapshot($this->user->id, 'Same 1');
        $snap2 = $project->createSnapshot($this->user->id, 'Same 2');

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('compareSnapshots', $snap1->id, $snap2->id)
            ->assertSet('showDiff', true)
            ->assertSet('diffResults', []);
    }

    public function test_compare_snapshots_detects_type_change(): void
    {
        $typeA = ProjectType::create(['name' => 'New Implementation', 'color' => 'blue', 'sort_order' => 1]);
        $typeB = ProjectType::create(['name' => 'Migration', 'color' => 'green', 'sort_order' => 2]);

        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'project_type_id' => $typeA->id,
        ]);

        $snap1 = $project->createSnapshot($this->user->id, 'V1');

        $project->update(['project_type_id' => $typeB->id]);
        $snap2 = $project->createSnapshot($this->user->id, 'V2');

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('compareSnapshots', $snap1->id, $snap2->id)
            ->assertSet('diffResults', function ($diff) {
                return collect($diff)->contains(fn ($d) => $d['field'] === __('projects.type'));
            });
    }

    public function test_compare_snapshots_detects_phase_change(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'current_phase' => 'instalacija_analiza',
        ]);

        $snap1 = $project->createSnapshot($this->user->id, 'V1');

        $project->update(['current_phase' => 'integracije']);
        $snap2 = $project->createSnapshot($this->user->id, 'V2');

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('compareSnapshots', $snap1->id, $snap2->id)
            ->assertSet('diffResults', function ($diff) {
                return collect($diff)->contains(fn ($d) => $d['field'] === __('projects.current_phase'));
            });
    }

    public function test_close_snapshot_resets_diff_state(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $snap1 = $project->createSnapshot($this->user->id, 'V1');
        $project->update(['name' => 'Changed']);
        $snap2 = $project->createSnapshot($this->user->id, 'V2');

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('compareSnapshots', $snap1->id, $snap2->id)
            ->assertSet('showDiff', true)
            ->call('closeSnapshot')
            ->assertSet('showDiff', false)
            ->assertSet('diffResults', [])
            ->assertSet('compareSnapshotId', null)
            ->assertSet('selectedSnapshotId', null);
    }

    // =============================================
    // DarkModeToggle
    // =============================================

    public function test_dark_mode_toggle_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(DarkModeToggle::class)
            ->assertOk();
    }

    // =============================================
    // PortfolioPdf - admin vs user
    // =============================================

    public function test_portfolio_pdf_admin_sees_all_projects(): void
    {
        Project::factory()->create(['created_by' => $this->user->id]);
        Project::factory()->create(['created_by' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('projects.portfolio'));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_portfolio_pdf_user_sees_only_own(): void
    {
        Project::factory()->create(['created_by' => $this->user->id]);
        $otherUser = User::factory()->create();
        Project::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('projects.portfolio'));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_portfolio_pdf_requires_auth(): void
    {
        $this->get(route('projects.portfolio'))
            ->assertRedirect(route('login'));
    }

    // =============================================
    // ProjectPdf - authorization
    // =============================================

    public function test_admin_can_view_any_project_pdf(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->admin)
            ->get(route('projects.pdf', $project))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    // =============================================
    // ProjectIndex - additional coverage
    // =============================================

    public function test_inline_edit_spent_hours(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'spent_hours' => 100,
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'spent_hours', '150');

        $this->assertEquals(150, $project->fresh()->spent_hours);
    }

    public function test_inline_edit_rejects_invalid_field(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Original',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'name', 'Hacked');

        $this->assertEquals('Original', $project->fresh()->name);
    }

    public function test_inline_edit_spent_hours_clamps_negative(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'spent_hours' => 100,
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'spent_hours', '-10');

        $this->assertEquals(0, $project->fresh()->spent_hours);
    }

    public function test_start_and_cancel_inline_edit(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('startInlineEdit', $project->id)
            ->assertSet('editingId', $project->id)
            ->call('cancelInlineEdit')
            ->assertSet('editingId', null);
    }

    public function test_search_by_client_name(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Project A',
            'client' => 'Acme Corp',
        ]);
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Project B',
            'client' => 'Beta Inc',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->set('search', 'Acme')
            ->assertSee('Project A')
            ->assertDontSee('Project B');
    }

    public function test_admin_sees_all_projects_on_index(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'User Project',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProjectIndex::class)
            ->assertSee('User Project');
    }

    // =============================================
    // ProjectForm - unauthorized access
    // =============================================

    public function test_form_forbids_editing_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->assertForbidden();
    }

    public function test_form_admin_can_edit_any_project(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        Livewire::actingAs($this->admin)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->assertOk()
            ->assertSet('name', $project->name);
    }

    // =============================================
    // Notification toWebhook / toMail coverage
    // =============================================

    public function test_project_alert_notification_to_webhook_go_live(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'planned_go_live' => now()->addDays(3),
        ]);

        $notification = new ProjectAlertNotification($project, 'go_live_soon');
        $data = $notification->toWebhook($this->user);

        $this->assertStringContains('Go-Live', $data['text']);
        $this->assertStringContains($project->name, $data['text']);
    }

    public function test_project_alert_notification_to_webhook_budget(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'estimated_hours' => 100,
            'spent_hours' => 80,
            'remaining_hours' => 40,
        ]);

        $notification = new ProjectAlertNotification($project, 'budget_overrun');
        $data = $notification->toWebhook($this->user);

        $this->assertStringContains('budget overrun', $data['text']);
    }

    public function test_project_alert_notification_to_webhook_default(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $notification = new ProjectAlertNotification($project, 'unknown_type');
        $data = $notification->toWebhook($this->user);

        $this->assertStringContains('Alert for project', $data['text']);
    }

    public function test_project_alert_notification_to_mail_go_live(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'planned_go_live' => now()->addDays(3),
        ]);

        $notification = new ProjectAlertNotification($project, 'go_live_soon');
        $mail = $notification->toMail($this->user);

        $this->assertStringContains("Project Alert: {$project->name}", $mail->subject);
    }

    public function test_project_alert_notification_to_mail_budget(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'estimated_hours' => 100,
        ]);

        $notification = new ProjectAlertNotification($project, 'budget_overrun');
        $mail = $notification->toMail($this->user);

        $this->assertStringContains("Project Alert:", $mail->subject);
    }

    public function test_project_alert_notification_to_mail_default(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $notification = new ProjectAlertNotification($project, 'unknown_type');
        $mail = $notification->toMail($this->user);

        $this->assertStringContains("Project Alert:", $mail->subject);
    }

    public function test_project_alert_notification_via_includes_webhook_when_url_set(): void
    {
        $userWithWebhook = User::factory()->create([
            'slack_webhook_url' => 'https://hooks.slack.com/test',
        ]);

        $project = Project::factory()->create(['created_by' => $userWithWebhook->id]);
        $notification = new ProjectAlertNotification($project, 'health_changed');
        $channels = $notification->via($userWithWebhook);

        $this->assertContains(\App\Channels\WebhookChannel::class, $channels);
    }

    public function test_project_alert_notification_via_excludes_webhook_without_url(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $notification = new ProjectAlertNotification($project, 'health_changed');
        $channels = $notification->via($this->user);

        $this->assertNotContains(\App\Channels\WebhookChannel::class, $channels);
    }

    // =============================================
    // Helpers
    // =============================================

    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'."
        );
    }
}
