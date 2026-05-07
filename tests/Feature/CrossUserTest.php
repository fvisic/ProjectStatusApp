<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Livewire\ProjectForm;
use App\Livewire\ProjectIndex;
use App\Livewire\ProjectKanban;
use App\Livewire\UserIndex;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests for cross-user scenarios, ownership invariants, and authorization
 * boundaries that were missing from the original test suite.
 */
class CrossUserTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // 1. Ownership invariants - created_by must NEVER change on edit
    // ---------------------------------------------------------------

    public function test_admin_editing_project_does_not_change_created_by(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create(['created_by' => $owner->id]);

        Livewire::actingAs($admin)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('name', 'Changed By Admin')
            ->call('save');

        $fresh = $project->fresh();
        $this->assertEquals($owner->id, $fresh->created_by);
        $this->assertEquals($admin->id, $fresh->updated_by);
    }

    public function test_manager_editing_own_project_preserves_created_by(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $project = Project::factory()->create(['created_by' => $manager->id]);

        Livewire::actingAs($manager)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('name', 'Manager Edit')
            ->call('save');

        $this->assertEquals($manager->id, $project->fresh()->created_by);
    }

    public function test_inline_edit_does_not_change_created_by(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create(['created_by' => $owner->id]);

        Livewire::actingAs($admin)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'off_track');

        $fresh = $project->fresh();
        $this->assertEquals($owner->id, $fresh->created_by);
        $this->assertEquals($admin->id, $fresh->updated_by);
    }

    public function test_inline_edit_spent_hours_sets_updated_by(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create(['created_by' => $owner->id, 'spent_hours' => 100]);

        Livewire::actingAs($admin)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'spent_hours', '150');

        $fresh = $project->fresh();
        $this->assertEquals(150, $fresh->spent_hours);
        $this->assertEquals($owner->id, $fresh->created_by);
        $this->assertEquals($admin->id, $fresh->updated_by);
    }

    public function test_kanban_drag_does_not_change_created_by(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create([
            'created_by' => $owner->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($admin)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'off_track');

        $fresh = $project->fresh();
        $this->assertEquals($owner->id, $fresh->created_by);
        $this->assertEquals($admin->id, $fresh->updated_by);
        $this->assertEquals('off_track', $fresh->overall_health);
    }

    // ---------------------------------------------------------------
    // 2. Manager authorization boundaries
    // ---------------------------------------------------------------

    public function test_manager_sees_other_users_project_in_list_but_cannot_edit(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $owner = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $owner->id, 'name' => 'Owner Only Edit']);

        // Manager sees it in the list
        Livewire::actingAs($manager)
            ->test(ProjectIndex::class)
            ->assertSee('Owner Only Edit');

        // Manager can open the edit form in read-only mode (view policy allows managers)
        $this->actingAs($manager)
            ->get(route('projects.edit', $project))
            ->assertOk();

        // But saving changes is blocked by the update policy
        Livewire::actingAs($manager)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('name', 'Hijacked Name')
            ->call('save')
            ->assertForbidden();
    }

    public function test_manager_cannot_delete_other_users_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $owner = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $owner->id]);

        Livewire::actingAs($manager)
            ->test(ProjectIndex::class)
            ->call('deleteProject', $project->id)
            ->assertForbidden();

        $this->assertNotNull($project->fresh());
    }

    public function test_manager_cannot_inline_edit_other_users_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $owner = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $owner->id, 'overall_health' => 'on_track']);

        Livewire::actingAs($manager)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'off_track')
            ->assertForbidden();

        $this->assertEquals('on_track', $project->fresh()->overall_health);
    }

    public function test_manager_cannot_drag_other_users_project_on_kanban(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $owner = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create([
            'created_by' => $owner->id,
            'overall_health' => 'on_track',
        ]);

        Livewire::actingAs($manager)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'off_track')
            ->assertForbidden();

        $this->assertEquals('on_track', $project->fresh()->overall_health);
    }

    // ---------------------------------------------------------------
    // 3. Regular user cannot see other users' projects
    // ---------------------------------------------------------------

    public function test_user_cannot_see_other_users_projects_in_list(): void
    {
        $alice = User::factory()->create(['role' => 'user']);
        $bob = User::factory()->create(['role' => 'user']);
        $aliceProject = Project::factory()->create(['created_by' => $alice->id, 'name' => 'Alice Project']);
        $bobProject = Project::factory()->create(['created_by' => $bob->id, 'name' => 'Bob Secret Project']);

        Livewire::actingAs($alice)
            ->test(ProjectIndex::class)
            ->assertSee('Alice Project')
            ->assertDontSee('Bob Secret Project');
    }

    public function test_user_cannot_see_other_users_projects_on_kanban(): void
    {
        $alice = User::factory()->create(['role' => 'user']);
        $bob = User::factory()->create(['role' => 'user']);
        Project::factory()->create(['created_by' => $alice->id, 'name' => 'Alice Kanban']);
        Project::factory()->create(['created_by' => $bob->id, 'name' => 'Bob Kanban']);

        Livewire::actingAs($alice)
            ->test(ProjectKanban::class)
            ->assertSee('Alice Kanban')
            ->assertDontSee('Bob Kanban');
    }

    public function test_user_cannot_see_other_users_projects_on_dashboard(): void
    {
        $alice = User::factory()->create(['role' => 'user']);
        $bob = User::factory()->create(['role' => 'user']);
        Project::factory()->create(['created_by' => $alice->id, 'name' => 'Alice Dashboard']);
        Project::factory()->create(['created_by' => $bob->id, 'name' => 'Bob Dashboard']);

        Livewire::actingAs($alice)
            ->test(Dashboard::class)
            ->assertSee('Alice Dashboard')
            ->assertDontSee('Bob Dashboard');
    }

    // ---------------------------------------------------------------
    // 4. Manager sees ALL projects (read-only)
    // ---------------------------------------------------------------

    public function test_manager_sees_all_projects_in_list(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $user = User::factory()->create(['role' => 'user']);
        Project::factory()->create(['created_by' => $manager->id, 'name' => 'Manager Own']);
        Project::factory()->create(['created_by' => $user->id, 'name' => 'User Project Visible']);

        Livewire::actingAs($manager)
            ->test(ProjectIndex::class)
            ->assertSee('Manager Own')
            ->assertSee('User Project Visible');
    }

    public function test_manager_sees_all_projects_on_dashboard(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $user = User::factory()->create(['role' => 'user']);
        Project::factory()->create(['created_by' => $user->id, 'name' => 'User Dashboard Visible']);

        Livewire::actingAs($manager)
            ->test(Dashboard::class)
            ->assertSee('User Dashboard Visible');
    }

    // ---------------------------------------------------------------
    // 5. Delete user cascades projects correctly
    // ---------------------------------------------------------------

    public function test_deleting_user_cascades_their_projects(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $target->id]);

        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('deleteUser', $target->id);

        $this->assertNull(User::find($target->id));
        $this->assertNull(Project::find($project->id));
    }

    // ---------------------------------------------------------------
    // 6. Role change immediately affects authorization
    // ---------------------------------------------------------------

    public function test_demoted_user_loses_access_to_all_projects(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create(['role' => 'user']);
        $otherProject = Project::factory()->create(['created_by' => $other->id, 'name' => 'Other Secret']);

        // Manager can see all projects
        Livewire::actingAs($manager)
            ->test(ProjectIndex::class)
            ->assertSee('Other Secret');

        // Admin demotes manager to user
        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('changeRole', $manager->id, 'user');

        // Demoted user can no longer see other's projects
        $manager->refresh();
        Livewire::actingAs($manager)
            ->test(ProjectIndex::class)
            ->assertDontSee('Other Secret');
    }

    // ---------------------------------------------------------------
    // 7. Comment authorization
    // ---------------------------------------------------------------

    public function test_comment_owner_can_delete_own_comment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $user->id]);
        $comment = $project->comments()->create([
            'user_id' => $user->id,
            'body' => 'My comment',
        ]);

        Livewire::actingAs($user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->call('deleteComment', $comment->id);

        $this->assertDatabaseMissing('project_comments', ['id' => $comment->id]);
    }

    public function test_other_user_cannot_access_form_for_foreign_project(): void
    {
        $alice = User::factory()->create(['role' => 'user']);
        $bob = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $bob->id]);

        // Alice can't even open the edit form for Bob's project
        $this->actingAs($alice)
            ->get(route('projects.edit', $project))
            ->assertForbidden();
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $user->id]);
        $comment = $project->comments()->create([
            'user_id' => $user->id,
            'body' => 'User comment',
        ]);

        Livewire::actingAs($admin)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->call('deleteComment', $comment->id);

        $this->assertDatabaseMissing('project_comments', ['id' => $comment->id]);
    }

    // ---------------------------------------------------------------
    // 8. Disabled user cannot log in
    // ---------------------------------------------------------------

    public function test_disabled_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'is_disabled' => true,
            'password' => bcrypt('password'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    // ---------------------------------------------------------------
    // 9. SendProjectAlerts edge cases
    // ---------------------------------------------------------------

    public function test_alerts_skip_disabled_project_owners(): void
    {
        Notification::fake();

        $owner = User::factory()->create(['is_disabled' => true]);
        Project::factory()->create([
            'created_by' => $owner->id,
            'overall_health' => 'off_track',
        ]);

        // The command loads creator and notifies - disabled user shouldn't crash it
        $this->artisan('projects:send-alerts')->assertSuccessful();
    }

    // ---------------------------------------------------------------
    // 10. Burndown with no snapshots
    // ---------------------------------------------------------------

    public function test_burndown_with_no_snapshots_returns_empty(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $user->id]);

        $component = Livewire::actingAs($user)
            ->test(ProjectForm::class, ['projectId' => $project->id]);

        $this->assertIsArray($component->get('burndownData'));
    }

    // ---------------------------------------------------------------
    // 11. Snapshot created on save
    // ---------------------------------------------------------------

    public function test_saving_project_creates_snapshot(): void
    {
        $user = User::factory()->create();
        $type = ProjectType::create(['name' => 'Test Type', 'color' => 'blue', 'sort_order' => 1]);

        Livewire::actingAs($user)
            ->test(ProjectForm::class)
            ->set('name', 'Snapshot Test Project')
            ->set('client', 'Test Client')
            ->set('project_type_id', $type->id)
            ->call('save');

        $project = Project::where('name', 'Snapshot Test Project')->first();
        $this->assertNotNull($project);
        $this->assertGreaterThanOrEqual(1, $project->snapshots()->count());
    }

    public function test_kanban_drag_creates_snapshot(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_by' => $user->id,
            'overall_health' => 'on_track',
        ]);

        $snapshotsBefore = $project->snapshots()->count();

        Livewire::actingAs($user)
            ->test(ProjectKanban::class)
            ->call('updateHealth', $project->id, 'at_risk');

        $this->assertEquals($snapshotsBefore + 1, $project->snapshots()->count());
    }

    public function test_inline_edit_creates_snapshot(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $user->id]);
        $snapshotsBefore = $project->snapshots()->count();

        Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->call('saveInlineEdit', $project->id, 'overall_health', 'off_track');

        $this->assertEquals($snapshotsBefore + 1, $project->snapshots()->count());
    }

    // ---------------------------------------------------------------
    // 12. New project gets correct created_by and updated_by
    // ---------------------------------------------------------------

    public function test_new_project_sets_both_created_by_and_updated_by(): void
    {
        $user = User::factory()->create();
        $type = ProjectType::create(['name' => 'Test Type', 'color' => 'blue', 'sort_order' => 1]);

        Livewire::actingAs($user)
            ->test(ProjectForm::class)
            ->set('name', 'Ownership Test')
            ->set('project_type_id', $type->id)
            ->call('save');

        $project = Project::where('name', 'Ownership Test')->first();
        $this->assertEquals($user->id, $project->created_by);
        $this->assertEquals($user->id, $project->updated_by);
    }
}
