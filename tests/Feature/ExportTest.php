<?php

namespace Tests\Feature;

use App\Exports\ProjectsExport;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportTest extends TestCase
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

    public function test_export_query_returns_only_user_projects(): void
    {
        $own = Project::factory()->create(['created_by' => $this->user->id]);
        $other = Project::factory()->create(); // another user's project

        $export = new ProjectsExport($this->user->id, false);
        $results = $export->query()->get();

        $this->assertTrue($results->contains('id', $own->id));
        $this->assertFalse($results->contains('id', $other->id));
    }

    public function test_export_query_returns_all_projects_for_admin(): void
    {
        $own = Project::factory()->create(['created_by' => $this->admin->id]);
        $other = Project::factory()->create(['created_by' => $this->user->id]);

        $export = new ProjectsExport($this->admin->id, true);
        $results = $export->query()->get();

        $this->assertTrue($results->contains('id', $own->id));
        $this->assertTrue($results->contains('id', $other->id));
    }

    public function test_export_query_filters_by_health(): void
    {
        $onTrack = Project::factory()->create(['created_by' => $this->user->id, 'overall_health' => 'on_track']);
        $offTrack = Project::factory()->create(['created_by' => $this->user->id, 'overall_health' => 'off_track']);

        $export = new ProjectsExport($this->user->id, false, filterHealth: 'on_track');
        $results = $export->query()->get();

        $this->assertTrue($results->contains('id', $onTrack->id));
        $this->assertFalse($results->contains('id', $offTrack->id));
    }

    public function test_export_query_filters_by_type(): void
    {
        $typeA = ProjectType::create(['name' => 'New Implementation', 'color' => 'blue', 'sort_order' => 1]);
        $typeB = ProjectType::create(['name' => 'CR', 'color' => 'yellow', 'sort_order' => 3]);

        $new = Project::factory()->create(['created_by' => $this->user->id, 'project_type_id' => $typeA->id]);
        $cr = Project::factory()->create(['created_by' => $this->user->id, 'project_type_id' => $typeB->id]);

        $export = new ProjectsExport($this->user->id, false, filterType: $typeA->id);
        $results = $export->query()->get();

        $this->assertTrue($results->contains('id', $new->id));
        $this->assertFalse($results->contains('id', $cr->id));
    }

    public function test_export_headings_returns_correct_columns(): void
    {
        $export = new ProjectsExport($this->user->id);
        $headings = $export->headings();

        $this->assertCount(13, $headings);
        $this->assertContains('Delta %', $headings);
    }

    public function test_export_map_returns_correct_data(): void
    {
        $type = ProjectType::create(['name' => 'New Implementation', 'color' => 'blue', 'sort_order' => 1]);

        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Map Test',
            'client' => 'Client X',
            'project_type_id' => $type->id,
            'current_phase' => 'integracije',
            'overall_health' => 'on_track',
            'estimated_hours' => 200,
            'spent_hours' => 100,
            'remaining_hours' => 80,
            'version' => 'v2.0',
        ]);
        $project->load('projectType');

        $export = new ProjectsExport($this->user->id);
        $mapped = $export->map($project);

        $this->assertEquals('Map Test', $mapped[0]);
        $this->assertEquals('Client X', $mapped[1]);
        $this->assertEquals(200, $mapped[8]);
        $this->assertEquals(100, $mapped[9]);
        $this->assertEquals(80, $mapped[10]);
        $this->assertEquals('v2.0', $mapped[12]);
    }

    public function test_export_map_delta_calculation_positive(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'estimated_hours' => 100,
            'spent_hours' => 80,
            'remaining_hours' => 40,  // forecast = 120, delta = +20%
        ]);

        $export = new ProjectsExport($this->user->id);
        $mapped = $export->map($project);

        $this->assertEquals('+20%', $mapped[11]);
    }

    public function test_export_map_delta_calculation_negative(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'estimated_hours' => 200,
            'spent_hours' => 50,
            'remaining_hours' => 100,  // forecast = 150, delta = -25%
        ]);

        $export = new ProjectsExport($this->user->id);
        $mapped = $export->map($project);

        $this->assertEquals('-25%', $mapped[11]);
    }

    public function test_export_map_delta_zero_when_no_estimate(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'estimated_hours' => null,
            'spent_hours' => 50,
            'remaining_hours' => 100,
        ]);

        $export = new ProjectsExport($this->user->id);
        $mapped = $export->map($project);

        $this->assertEquals('0%', $mapped[11]);
    }

    public function test_export_map_handles_null_fields(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'client' => null,
            'team_lead' => null,
            'project_start' => null,
            'planned_go_live' => null,
            'estimated_hours' => null,
            'spent_hours' => null,
            'remaining_hours' => null,
        ]);

        $export = new ProjectsExport($this->user->id);
        $mapped = $export->map($project);

        $this->assertEquals('', $mapped[1]); // client
        $this->assertEquals('', $mapped[5]); // team_lead
        $this->assertEquals('', $mapped[6]); // project_start
        $this->assertEquals('', $mapped[7]); // planned_go_live
        $this->assertEquals(0, $mapped[8]);  // estimated_hours
        $this->assertEquals(0, $mapped[9]);  // spent_hours
        $this->assertEquals(0, $mapped[10]); // remaining_hours
    }

    public function test_export_route_downloads_xlsx(): void
    {
        Excel::fake();

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)
            ->get(route('projects.export', ['format' => 'xlsx']))
            ->assertOk();
    }

    public function test_export_route_downloads_csv(): void
    {
        Excel::fake();

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)
            ->get(route('projects.export', ['format' => 'csv']))
            ->assertOk();
    }

    public function test_export_route_requires_auth(): void
    {
        $this->get(route('projects.export', ['format' => 'xlsx']))
            ->assertRedirect(route('login'));
    }

    public function test_export_query_combines_health_and_type_filters(): void
    {
        $typeMig = ProjectType::create(['name' => 'Migration', 'color' => 'green', 'sort_order' => 2]);
        $typeNew = ProjectType::create(['name' => 'New Implementation', 'color' => 'blue', 'sort_order' => 1]);

        $match = Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'at_risk',
            'project_type_id' => $typeMig->id,
        ]);
        Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
            'project_type_id' => $typeMig->id,
        ]);
        Project::factory()->create([
            'created_by' => $this->user->id,
            'overall_health' => 'at_risk',
            'project_type_id' => $typeNew->id,
        ]);

        $export = new ProjectsExport($this->user->id, false, filterHealth: 'at_risk', filterType: $typeMig->id);
        $results = $export->query()->get();

        $this->assertCount(1, $results);
        $this->assertEquals($match->id, $results->first()->id);
    }
}
