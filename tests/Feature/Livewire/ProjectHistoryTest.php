<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProjectHistory;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_history_page_renders(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->assertOk();
    }

    public function test_history_shows_snapshots(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->createSnapshot($this->user->id, 'First save');

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->assertSee('First save');
    }

    public function test_view_snapshot_loads_data(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'History Test',
        ]);
        $snapshot = $project->createSnapshot($this->user->id);

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('viewSnapshot', $snapshot->id)
            ->assertSet('selectedSnapshotId', $snapshot->id)
            ->assertSet('selectedSnapshot', function ($data) {
                return $data['name'] === 'History Test';
            });
    }

    public function test_close_snapshot_clears_selection(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $snapshot = $project->createSnapshot($this->user->id);

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->call('viewSnapshot', $snapshot->id)
            ->call('closeSnapshot')
            ->assertSet('selectedSnapshot', null)
            ->assertSet('selectedSnapshotId', null);
    }

    public function test_cannot_view_other_users_project_history(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(ProjectHistory::class, ['projectId' => $project->id])
            ->assertForbidden();
    }

    public function test_multiple_snapshots_are_ordered_by_date_desc(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->createSnapshot($this->user->id, 'First');

        // Ensure different timestamps
        $this->travel(1)->minutes();
        $project->createSnapshot($this->user->id, 'Second');

        $snapshots = $project->fresh()->snapshots;
        $this->assertEquals('Second', $snapshots->first()->change_note);
        $this->assertEquals('First', $snapshots->last()->change_note);
    }
}
