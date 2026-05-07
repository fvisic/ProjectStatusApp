<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectRisk;
use App\Models\ProjectNextStep;
use App\Models\ProjectSnapshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // --- Model & relationship tests ---

    public function test_project_belongs_to_user(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $this->assertInstanceOf(User::class, $project->creator);
        $this->assertEquals($this->user->id, $project->creator->id);
    }

    public function test_project_has_many_phases(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->phases()->create([
            'phase_name' => 'Test Phase',
            'sort_order' => 1,
        ]);

        $this->assertCount(1, $project->phases);
        $this->assertInstanceOf(ProjectPhase::class, $project->phases->first());
    }

    public function test_project_has_many_risks(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->risks()->create([
            'description' => 'Test Risk',
            'level' => 'high',
            'sort_order' => 1,
        ]);

        $this->assertCount(1, $project->risks);
        $this->assertInstanceOf(ProjectRisk::class, $project->risks->first());
    }

    public function test_project_has_many_next_steps(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->nextSteps()->create([
            'description' => 'Step 1',
            'sort_order' => 1,
        ]);

        $this->assertCount(1, $project->nextSteps);
        $this->assertInstanceOf(ProjectNextStep::class, $project->nextSteps->first());
    }

    public function test_project_has_many_snapshots(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->createSnapshot($this->user->id, 'Initial');

        $this->assertCount(1, $project->snapshots);
        $this->assertInstanceOf(ProjectSnapshot::class, $project->snapshots->first());
    }

    public function test_create_snapshot_stores_full_project_data(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id, 'name' => 'Snapshot Test']);
        $project->phases()->create([
            'phase_name' => 'Phase 1',
            'status' => 'done',
            'sort_order' => 1,
        ]);
        $project->risks()->create([
            'description' => 'Risk 1',
            'level' => 'medium',
            'sort_order' => 1,
        ]);

        $snapshot = $project->createSnapshot($this->user->id, 'Test snapshot');

        $this->assertEquals('Snapshot Test', $snapshot->snapshot_data['name']);
        $this->assertCount(1, $snapshot->snapshot_data['phases']);
        $this->assertCount(1, $snapshot->snapshot_data['risks']);
        $this->assertEquals('Test snapshot', $snapshot->change_note);
    }

    public function test_soft_delete_keeps_record_in_database(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $projectId = $project->id;

        $project->delete();

        $this->assertSoftDeleted('projects', ['id' => $projectId]);
    }

    public function test_force_delete_removes_related_records(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $project->phases()->create(['phase_name' => 'P1', 'sort_order' => 1]);
        $project->risks()->create(['description' => 'R1', 'level' => 'low', 'sort_order' => 1]);
        $project->nextSteps()->create(['description' => 'S1', 'sort_order' => 1]);
        $project->createSnapshot($this->user->id);

        $projectId = $project->id;
        $project->forceDelete();

        $this->assertDatabaseMissing('projects', ['id' => $projectId]);
        $this->assertDatabaseMissing('project_phases', ['project_id' => $projectId]);
        $this->assertDatabaseMissing('project_risks', ['project_id' => $projectId]);
        $this->assertDatabaseMissing('project_next_steps', ['project_id' => $projectId]);
        $this->assertDatabaseMissing('project_snapshots', ['project_id' => $projectId]);
    }

    // --- Auth & access tests ---

    public function test_guest_is_redirected_from_projects(): void
    {
        $this->get(route('projects.index'))->assertRedirect(route('login'));
        $this->get(route('projects.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_projects_index(): void
    {
        $this->actingAs($this->user)
            ->get(route('projects.index'))
            ->assertOk();
    }

    public function test_authenticated_user_can_access_create_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('projects.create'))
            ->assertOk();
    }

    public function test_authenticated_user_can_access_edit_form(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('projects.edit', $project->id))
            ->assertOk();
    }

    public function test_user_cannot_access_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);

        $this->actingAs($this->user)
            ->get(route('projects.edit', $project->id))
            ->assertForbidden();
    }

    public function test_authenticated_user_can_access_history(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('projects.history', $project->id))
            ->assertOk();
    }

    // --- PDF tests ---

    public function test_authenticated_user_can_download_pdf(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('projects.pdf', $project))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_user_cannot_download_other_users_pdf(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $otherUser->id]);

        $this->actingAs($this->user)
            ->get(route('projects.pdf', $project))
            ->assertForbidden();
    }

    // --- Static data tests ---

    public function test_phase_labels_contain_all_values(): void
    {
        $expected = [
            'instalacija_analiza', 'funkcionalna_specifikacija',
            'implementacija_testiranje', 'integracije',
            'uat_edukacija', 'go_live', 'hypercare',
        ];

        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, Project::$phaseLabels);
        }
    }

    public function test_default_phases_are_defined(): void
    {
        $this->assertCount(7, ProjectPhase::$defaultPhases);
    }

    public function test_project_type_model_stores_name_and_color(): void
    {
        $type = \App\Models\ProjectType::create(['name' => 'New Implementation', 'color' => 'blue', 'sort_order' => 1]);

        $this->assertDatabaseHas('project_types', ['name' => 'New Implementation', 'color' => 'blue']);
        $this->assertNotEmpty($type->badgeClass());
        $this->assertNotEmpty($type->swatchClass());
    }
}
