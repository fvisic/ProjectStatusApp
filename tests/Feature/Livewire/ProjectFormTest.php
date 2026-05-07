<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProjectForm;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectSnapshot;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectFormTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ProjectType $projectType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->projectType = ProjectType::create(['name' => 'Test Type', 'color' => 'blue', 'sort_order' => 1]);
    }

    public function test_create_form_renders_with_default_phases(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->assertSet('phases', function ($phases) {
                return count($phases) === 7;
            })
            ->assertOk();
    }

    public function test_form_requires_project_name(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_form_creates_new_project(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Test Project')
            ->set('client', 'Test Client')
            ->set('team_lead', 'John Doe')
            ->set('project_type_id', $this->projectType->id)
            ->set('current_phase', 'instalacija_analiza')
            ->set('overall_health', 'on_track')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'client' => 'Test Client',
            'team_lead' => 'John Doe',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_form_creates_snapshot_on_save(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Snapshot Project')
            ->set('project_type_id', $this->projectType->id)
            ->call('save');

        $project = Project::where('name', 'Snapshot Project')->first();
        $this->assertNotNull($project);
        $this->assertCount(1, $project->snapshots);
    }

    public function test_form_saves_phases(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Phase Test')
            ->set('project_type_id', $this->projectType->id)
            ->set('phases.0.status', 'done')
            ->set('phases.0.notes', 'Completed successfully')
            ->call('save');

        $project = Project::where('name', 'Phase Test')->first();
        $this->assertEquals('done', $project->phases->first()->status);
        $this->assertEquals('Completed successfully', $project->phases->first()->notes);
    }

    public function test_form_saves_risks(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Risk Test')
            ->set('project_type_id', $this->projectType->id)
            ->set('risks.0.description', 'Client delay')
            ->set('risks.0.level', 'high')
            ->set('risks.0.mitigation', 'Escalate')
            ->call('save');

        $project = Project::where('name', 'Risk Test')->first();
        $risk = $project->risks->first();
        $this->assertEquals('Client delay', $risk->description);
        $this->assertEquals('high', $risk->level);
        $this->assertEquals('Escalate', $risk->mitigation);
    }

    public function test_form_saves_next_steps(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Steps Test')
            ->set('project_type_id', $this->projectType->id)
            ->set('nextSteps.0.description', 'First step')
            ->set('nextSteps.0.is_completed', true)
            ->call('save');

        $project = Project::where('name', 'Steps Test')->first();
        $step = $project->nextSteps->first();
        $this->assertEquals('First step', $step->description);
        $this->assertTrue($step->is_completed);
    }

    public function test_add_risk_adds_new_entry(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $initialCount = count($component->get('risks'));
        $component->call('addRisk');
        $this->assertCount($initialCount + 1, $component->get('risks'));
    }

    public function test_remove_risk_removes_entry(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $initialCount = count($component->get('risks'));
        $component->call('removeRisk', 0);
        $this->assertCount($initialCount - 1, $component->get('risks'));
    }

    public function test_add_next_step_adds_new_entry(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $initialCount = count($component->get('nextSteps'));
        $component->call('addNextStep');
        $this->assertCount($initialCount + 1, $component->get('nextSteps'));
    }

    public function test_remove_next_step_removes_entry(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $initialCount = count($component->get('nextSteps'));
        $component->call('removeNextStep', 0);
        $this->assertCount($initialCount - 1, $component->get('nextSteps'));
    }

    public function test_add_phase_appends_empty_phase(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $initialCount = count($component->get('phases'));
        $component->call('addPhase');
        $phases = $component->get('phases');

        $this->assertCount($initialCount + 1, $phases);
        $this->assertEquals('', $phases[$initialCount]['phase_name']);
        $this->assertEquals('pending', $phases[$initialCount]['status']);
    }

    public function test_remove_phase_drops_entry_and_reindexes(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $initialCount = count($component->get('phases'));
        $firstPhaseName = $component->get('phases')[0]['phase_name'];

        $component->call('removePhase', 0);
        $phases = $component->get('phases');

        $this->assertCount($initialCount - 1, $phases);
        $this->assertArrayHasKey(0, $phases);
        $this->assertNotEquals($firstPhaseName, $phases[0]['phase_name']);
    }

    public function test_reorder_phases_applies_new_order(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $original = $component->get('phases');
        $this->assertCount(7, $original);

        // Reverse order: [6,5,4,3,2,1,0]
        $reversed = array_reverse(array_keys($original));
        $component->call('reorderPhases', $reversed);

        $after = $component->get('phases');
        $this->assertEquals($original[6]['phase_name'], $after[0]['phase_name']);
        $this->assertEquals($original[0]['phase_name'], $after[6]['phase_name']);
    }

    public function test_reorder_phases_ignores_bad_input(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class);

        $before = $component->get('phases');
        // Incomplete order - should leave phases untouched
        $component->call('reorderPhases', [0, 1]);

        $this->assertEquals($before, $component->get('phases'));
    }

    public function test_form_saves_custom_phases(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Custom Phases Project')
            ->set('project_type_id', $this->projectType->id);

        // Start from default 7, remove all, then add 2 custom ones
        for ($i = 6; $i >= 0; $i--) {
            $component->call('removePhase', $i);
        }
        $component->call('addPhase');
        $component->call('addPhase');
        $component->set('phases.0.phase_name', 'Discovery');
        $component->set('phases.0.key_activities', 'Interviews, workshops');
        $component->set('phases.0.client_confirmation', 'Yes');
        $component->set('phases.1.phase_name', 'Rollout');
        $component->set('phases.1.status', 'in_progress');

        $component->call('save');

        $project = Project::where('name', 'Custom Phases Project')->first();
        $this->assertNotNull($project);
        $this->assertCount(2, $project->phases);
        $this->assertEquals('Discovery', $project->phases[0]->phase_name);
        $this->assertEquals('Interviews, workshops', $project->phases[0]->key_activities);
        $this->assertEquals(1, $project->phases[0]->sort_order);
        $this->assertEquals('Rollout', $project->phases[1]->phase_name);
        $this->assertEquals('in_progress', $project->phases[1]->status);
        $this->assertEquals(2, $project->phases[1]->sort_order);
    }

    public function test_form_persists_phase_order_after_reorder(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Reorder Persist')
            ->set('project_type_id', $this->projectType->id);

        $originalFirst = $component->get('phases')[0]['phase_name'];
        $originalLast = $component->get('phases')[6]['phase_name'];

        $reversed = array_reverse(array_keys($component->get('phases')));
        $component->call('reorderPhases', $reversed);
        $component->call('save');

        $project = Project::where('name', 'Reorder Persist')->first();
        $this->assertEquals($originalLast, $project->phases[0]->phase_name);
        $this->assertEquals($originalFirst, $project->phases[6]->phase_name);
    }

    public function test_edit_form_loads_existing_project_data(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Existing Project',
            'client' => 'Existing Client',
        ]);
        $project->phases()->create([
            'phase_name' => 'Phase 1',
            'status' => 'in_progress',
            'sort_order' => 1,
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->assertSet('name', 'Existing Project')
            ->assertSet('client', 'Existing Client')
            ->assertSet('phases', function ($phases) {
                return count($phases) === 1 && $phases[0]['status'] === 'in_progress';
            });
    }

    public function test_update_existing_project(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Old Name',
        ]);

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('name', 'New Name')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'New Name',
        ]);
    }

    public function test_save_with_changes_bumps_minor_version(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Bump Me',
            'version' => 'v1.0',
        ]);
        $project->createSnapshot($this->user->id, 'Initial');

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('name', 'Bumped Name')
            ->call('save');

        $this->assertEquals('v1.1', $project->fresh()->version);
        $this->assertCount(2, $project->fresh()->snapshots);
    }

    public function test_save_without_changes_does_not_bump_or_snapshot(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'No Changes',
            'version' => 'v1.5',
        ]);
        $project->createSnapshot($this->user->id, 'Initial');

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->call('save');

        $project->refresh();
        $this->assertEquals('v1.5', $project->version);
        $this->assertCount(1, $project->snapshots);
    }

    public function test_manual_version_edit_is_respected_and_not_overwritten(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Manual Bump',
            'version' => 'v1.7',
        ]);
        $project->createSnapshot($this->user->id, 'Initial');

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('name', 'Manual Bump v2')
            ->set('version', 'v2.0')
            ->call('save');

        // User explicitly bumped to v2.0 → respect, do not auto-bump to v1.8.
        $this->assertEquals('v2.0', $project->fresh()->version);
    }

    public function test_version_bump_handles_double_digit_minor(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Big Minor',
            'version' => 'v3.42',
        ]);
        $project->createSnapshot($this->user->id, 'Initial');

        Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('client', 'Acme')
            ->call('save');

        $this->assertEquals('v3.43', $project->fresh()->version);
    }

    public function test_phase_reorder_counts_as_a_change_and_bumps_version(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Reorder Bump',
            'version' => 'v1.0',
        ]);
        $project->phases()->create(['phase_name' => 'A', 'sort_order' => 1]);
        $project->phases()->create(['phase_name' => 'B', 'sort_order' => 2]);
        $project->createSnapshot($this->user->id, 'Initial');

        $component = Livewire::actingAs($this->user)
            ->test(ProjectForm::class, ['projectId' => $project->id]);

        $component->call('reorderPhases', [1, 0]);
        $component->call('save');

        $this->assertEquals('v1.1', $project->fresh()->version);
        $project->refresh()->load('phases');
        $this->assertEquals('B', $project->phases[0]->phase_name);
    }

    public function test_estimation_fields_are_saved(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Estimation Test')
            ->set('project_type_id', $this->projectType->id)
            ->set('estimated_hours', 200)
            ->set('spent_hours', 100)
            ->set('remaining_hours', 50)
            ->set('estimation_comment', 'On track')
            ->call('save');

        $project = Project::where('name', 'Estimation Test')->first();
        $this->assertEquals(200, $project->estimated_hours);
        $this->assertEquals(100, $project->spent_hours);
        $this->assertEquals(50, $project->remaining_hours);
        $this->assertEquals('On track', $project->estimation_comment);
    }

    public function test_product_notification_fields_are_saved(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProjectForm::class)
            ->set('name', 'Notif Test')
            ->set('project_type_id', $this->projectType->id)
            ->set('product_notification_deadline', '2026-05-01')
            ->set('product_notification_duration', '3-5 dana')
            ->set('product_notification_description', 'Fix needed')
            ->call('save');

        $project = Project::where('name', 'Notif Test')->first();
        $this->assertEquals('2026-05-01', $project->product_notification_deadline->format('Y-m-d'));
        $this->assertEquals('3-5 dana', $project->product_notification_duration);
        $this->assertEquals('Fix needed', $project->product_notification_description);
    }
}
