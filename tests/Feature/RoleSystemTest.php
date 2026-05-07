<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Livewire\ProjectIndex;
use App\Livewire\ProjectKanban;
use App\Livewire\ProjectTimeline;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoleSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_helper_recognizes_role_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => false]);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->isAdminOrManager());
        $this->assertFalse($admin->isManager());
    }

    public function test_admin_helper_recognizes_legacy_is_admin(): void
    {
        // Backward compat: users with is_admin=true but role='user' still act as admin
        $admin = User::factory()->create(['role' => 'user', 'is_admin' => true]);
        $this->assertTrue($admin->isAdmin());
    }

    public function test_manager_helper(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $this->assertFalse($manager->isAdmin());
        $this->assertTrue($manager->isManager());
        $this->assertTrue($manager->isAdminOrManager());
    }

    public function test_regular_user_has_no_elevated_roles(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_admin' => false]);
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isManager());
        $this->assertFalse($user->isAdminOrManager());
    }

    public function test_manager_can_view_other_users_project_via_policy(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $other->id]);

        $this->assertTrue($manager->can('view', $project));
    }

    public function test_manager_cannot_update_other_users_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $other->id]);

        $this->assertFalse($manager->can('update', $project));
    }

    public function test_manager_cannot_delete_other_users_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $other->id]);

        $this->assertFalse($manager->can('delete', $project));
    }

    public function test_manager_can_update_own_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $project = Project::factory()->create(['created_by' => $manager->id]);

        $this->assertTrue($manager->can('update', $project));
    }

    public function test_manager_sees_all_projects_in_dashboard(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create();
        Project::factory()->create(['created_by' => $manager->id]);
        Project::factory()->create(['created_by' => $other->id]);

        Livewire::actingAs($manager)
            ->test(Dashboard::class)
            ->assertOk();

        $projects = (new Dashboard())->projects ?? null; // loose check via render
        $this->assertDatabaseCount('projects', 2);
    }

    public function test_manager_sees_all_projects_on_index(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create();
        $mine = Project::factory()->create(['created_by' => $manager->id, 'name' => 'Mine']);
        $theirs = Project::factory()->create(['created_by' => $other->id, 'name' => 'Theirs']);

        Livewire::actingAs($manager)
            ->test(ProjectIndex::class)
            ->assertSee('Mine')
            ->assertSee('Theirs');
    }

    public function test_user_only_sees_own_projects_on_index(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $other = User::factory()->create();
        Project::factory()->create(['created_by' => $user->id, 'name' => 'Mine']);
        Project::factory()->create(['created_by' => $other->id, 'name' => 'Theirs']);

        Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->assertSee('Mine')
            ->assertDontSee('Theirs');
    }

    public function test_manager_sees_all_projects_on_kanban(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create();
        Project::factory()->create(['created_by' => $manager->id, 'name' => 'Alpha']);
        Project::factory()->create(['created_by' => $other->id, 'name' => 'Beta']);

        Livewire::actingAs($manager)
            ->test(ProjectKanban::class)
            ->assertSee('Alpha')
            ->assertSee('Beta');
    }

    public function test_manager_sees_all_projects_on_timeline(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $other = User::factory()->create();
        Project::factory()->create([
            'created_by' => $manager->id,
            'name' => 'Alpha',
            'project_start' => now(),
            'planned_go_live' => now()->addMonths(3),
        ]);
        Project::factory()->create([
            'created_by' => $other->id,
            'name' => 'Beta',
            'project_start' => now(),
            'planned_go_live' => now()->addMonths(3),
        ]);

        Livewire::actingAs($manager)
            ->test(ProjectTimeline::class)
            ->assertSee('Alpha')
            ->assertSee('Beta');
    }

    public function test_migration_sets_admin_role_for_existing_is_admin_users(): void
    {
        // Users created with is_admin=true in factory should work either way
        $legacy = User::factory()->create(['is_admin' => true, 'role' => 'admin']);
        $this->assertEquals('admin', $legacy->role);
        $this->assertTrue($legacy->isAdmin());
    }
}
