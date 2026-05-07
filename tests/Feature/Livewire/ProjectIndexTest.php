<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProjectIndex;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->assertOk();
    }

    public function test_index_shows_user_projects(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'My Project',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->assertSee('My Project');
    }

    public function test_index_does_not_show_other_users_projects(): void
    {
        $otherUser = User::factory()->create();
        Project::factory()->create([
            'created_by' => $otherUser->id,
            'name' => 'Secret Project',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->assertDontSee('Secret Project');
    }

    public function test_search_filters_projects(): void
    {
        Project::factory()->create(['created_by' => $this->user->id, 'name' => 'Alpha Project']);
        Project::factory()->create(['created_by' => $this->user->id, 'name' => 'Beta Project']);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->set('search', 'Alpha')
            ->assertSee('Alpha Project')
            ->assertDontSee('Beta Project');
    }

    public function test_health_filter_works(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Healthy Project',
            'overall_health' => 'on_track',
        ]);
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Risky Project',
            'overall_health' => 'off_track',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->set('filterHealth', 'on_track')
            ->assertSee('Healthy Project')
            ->assertDontSee('Risky Project');
    }

    public function test_type_filter_works(): void
    {
        $typeA = ProjectType::create(['name' => 'New Implementation', 'color' => 'blue', 'sort_order' => 1]);
        $typeB = ProjectType::create(['name' => 'Migration', 'color' => 'green', 'sort_order' => 2]);

        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'New Impl',
            'project_type_id' => $typeA->id,
        ]);
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Migration Proj',
            'project_type_id' => $typeB->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->set('filterType', $typeA->id)
            ->assertSee('New Impl')
            ->assertDontSee('Migration Proj');
    }

    public function test_delete_project_soft_deletes_it(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Delete Me',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('deleteProject', $project->id);

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_cannot_delete_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->call('deleteProject', $project->id)
            ->assertForbidden();
    }

    public function test_empty_state_shown_when_no_projects(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectIndex::class)
            ->assertSee(__('projects.no_projects'));
    }
}
