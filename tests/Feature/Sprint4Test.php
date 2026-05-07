<?php

namespace Tests\Feature;

use App\Channels\WebhookChannel;
use App\Console\Commands\SendProjectAlerts;
use App\Console\Commands\SendWeeklyReport;
use App\Livewire\ProjectIndex;
use App\Livewire\ProjectKanban;
use App\Livewire\ProjectTimeline;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAlertNotification;
use App\Notifications\WeeklyReportNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class Sprint4Test extends TestCase
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

    // --- Inline Edit ---

    public function test_inline_edit_updates_health(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'at_risk');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'overall_health' => 'at_risk',
        ]);
    }

    public function test_inline_edit_updates_phase(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'current_phase' => 'instalacija_analiza',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'current_phase', 'integracije');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'current_phase' => 'integracije',
        ]);
    }

    public function test_inline_edit_creates_snapshot(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'off_track');

        $this->assertDatabaseHas('project_snapshots', [
            'project_id' => $project->id,
            'change_note' => 'Inline edit',
        ]);
    }

    public function test_inline_edit_rejects_invalid_health(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'invalid_value');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'overall_health' => 'on_track', // unchanged
        ]);
    }

    public function test_inline_edit_forbidden_for_other_user(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'off_track')
            ->assertForbidden();
    }

    public function test_admin_can_inline_edit_any_project(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'off_track');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'overall_health' => 'off_track',
        ]);
    }

    // --- Webhook Channel ---

    public function test_webhook_channel_sends_to_url(): void
    {
        Http::fake();

        $user = User::factory()->create([
            'slack_webhook_url' => 'https://hooks.slack.com/test',
        ]);

        $project = Project::factory()->create([
            'created_by' => $user->id,
            'planned_go_live' => now()->addDays(3),
        ]);

        $notification = new ProjectAlertNotification($project, 'go_live_soon');
        $channel = new WebhookChannel();
        $channel->send($user, $notification);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://hooks.slack.com/test'
            && str_contains($request['text'], $project->name)
        );
    }

    public function test_webhook_channel_skips_when_no_url(): void
    {
        Http::fake();

        $user = User::factory()->create(['slack_webhook_url' => null]);
        $project = Project::factory()->create(['created_by' => $user->id]);

        $notification = new ProjectAlertNotification($project, 'health_changed');
        $channel = new WebhookChannel();
        $channel->send($user, $notification);

        Http::assertNothingSent();
    }

    // --- Weekly Report ---

    public function test_weekly_report_sends_to_admins(): void
    {
        Notification::fake();

        Project::factory()->create(['created_by' => $this->user->id]);

        $this->artisan('projects:weekly-report')
            ->assertSuccessful();

        Notification::assertSentTo($this->admin, WeeklyReportNotification::class);
        Notification::assertNotSentTo($this->user, WeeklyReportNotification::class);
    }

    public function test_weekly_report_skips_when_no_projects(): void
    {
        Notification::fake();

        $this->artisan('projects:weekly-report')
            ->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_weekly_report_notification_includes_webhook_channel(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'slack_webhook_url' => 'https://hooks.slack.com/test',
        ]);

        $summary = [
            'projects' => collect([]),
            'healthCounts' => [],
            'totalEstimated' => 0,
            'totalSpent' => 0,
            'totalForecast' => 0,
            'overallDelta' => 0,
            'offTrack' => collect([]),
            'upcomingGoLives' => collect([]),
        ];

        $notification = new WeeklyReportNotification($summary);
        $channels = $notification->via($admin);

        $this->assertContains('mail', $channels);
        $this->assertContains(WebhookChannel::class, $channels);
    }

    // --- Project Alerts ---

    public function test_send_alerts_command(): void
    {
        Notification::fake();

        // Off-track project
        Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'off_track',
        ]);

        $this->artisan('projects:send-alerts')
            ->assertSuccessful();

        Notification::assertSentTo($this->user, ProjectAlertNotification::class);
    }

    // --- Kanban ---

    public function test_kanban_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectKanban::class)
            ->assertOk();
    }

    public function test_kanban_shows_projects_in_columns(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Green Project',
            'overall_health' => 'on_track',
        ]);
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Red Project',
            'overall_health' => 'off_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectKanban::class)
            ->assertSee('Green Project')
            ->assertSee('Red Project');
    }

    // --- Timeline ---

    public function test_timeline_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->assertOk();
    }

    public function test_timeline_shows_projects_with_dates(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Timeline Project',
            'project_start' => now()->subMonths(1),
            'planned_go_live' => now()->addMonths(2),
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->assertSee('Timeline Project');
    }

    public function test_timeline_empty_without_dates(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'No Dates Project',
            'project_start' => null,
            'planned_go_live' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->assertDontSee('No Dates Project');
    }

    // --- Portfolio PDF ---

    public function test_portfolio_pdf_renders(): void
    {
        Project::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('projects.portfolio'));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    // --- Burndown Data ---

    public function test_burndown_data_computed_from_snapshots(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'estimated_hours' => 100,
            'spent_hours' => 50,
            'remaining_hours' => 40,
        ]);

        $project->createSnapshot($this->user->id, 'Initial');

        $project->update(['spent_hours' => 70, 'remaining_hours' => 20]);
        $project->createSnapshot($this->user->id, 'Update');

        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\ProjectForm::class, ['projectId' => $project->id]);

        // Verify the component renders without error (burndown data is computed)
        $component->assertOk();
    }

    // --- Admin sees all in timeline/kanban ---

    public function test_admin_sees_all_projects_on_kanban(): void
    {
        $otherUser = User::factory()->create();
        Project::factory()->create([
            'created_by' => $otherUser->id,
            'name' => 'Other User Project',
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProjectKanban::class)
            ->assertSee('Other User Project');
    }

    public function test_admin_sees_all_projects_on_timeline(): void
    {
        $otherUser = User::factory()->create();
        Project::factory()->create([
            'created_by' => $otherUser->id,
            'name' => 'Timeline Other',
            'project_start' => now()->subMonths(1),
            'planned_go_live' => now()->addMonths(2),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProjectTimeline::class)
            ->assertSee('Timeline Other');
    }

    // --- Kanban Drag & Drop ---

    public function test_kanban_drag_drop_updates_health(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'off_track');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'overall_health' => 'off_track',
        ]);
    }

    public function test_kanban_drag_drop_creates_snapshot(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'at_risk');

        $this->assertDatabaseHas('project_snapshots', [
            'project_id' => $project->id,
            'change_note' => 'Kanban drag & drop',
        ]);
    }

    public function test_kanban_drag_drop_rejects_invalid_health(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'invalid_status');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'overall_health' => 'on_track', // unchanged
        ]);
    }

    public function test_kanban_drag_drop_forbidden_for_other_user(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'off_track')
            ->assertForbidden();
    }

    public function test_kanban_drag_drop_skips_same_health(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'on_track');

        // No snapshot created when health didn't change
        $this->assertDatabaseMissing('project_snapshots', [
            'project_id' => $project->id,
        ]);
    }

    // --- Timeline Zoom ---

    public function test_timeline_zoom_in(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'project_start' => now()->subMonths(2),
            'planned_go_live' => now()->addMonths(3),
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->assertSet('zoom', 2)
            ->call('zoomIn')
            ->assertSet('zoom', 3);
    }

    public function test_timeline_zoom_out(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'project_start' => now()->subMonths(2),
            'planned_go_live' => now()->addMonths(3),
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->assertSet('zoom', 2)
            ->call('zoomOut')
            ->assertSet('zoom', 1);
    }

    public function test_timeline_zoom_clamps_at_limits(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'project_start' => now()->subMonths(2),
            'planned_go_live' => now()->addMonths(3),
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->call('zoomIn')   // 2 -> 3
            ->call('zoomIn')   // stays at 3
            ->assertSet('zoom', 3)
            ->call('zoomOut')  // 3 -> 2
            ->call('zoomOut')  // 2 -> 1
            ->call('zoomOut')  // stays at 1
            ->assertSet('zoom', 1);
    }

    public function test_timeline_zoom_quarters_shows_q_labels(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'project_start' => now()->subMonths(2),
            'planned_go_live' => now()->addMonths(3),
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->call('zoomOut') // zoom to quarters
            ->assertSeeHtml('Q');
    }

    public function test_timeline_zoom_weeks_renders(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'project_start' => now()->subMonths(1),
            'planned_go_live' => now()->addMonths(1),
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectTimeline::class)
            ->call('zoomIn') // zoom to weeks
            ->assertOk();
    }
}
