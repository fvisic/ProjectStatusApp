<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\ProjectSnapshot;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_seeder_creates_admin_user(): void
    {
        $admin = User::where('email', 'ana@firma.hr')->first();

        $this->assertNotNull($admin);
        $this->assertTrue((bool) $admin->is_admin);
    }

    public function test_seeder_creates_expected_users(): void
    {
        $this->assertEquals(4, User::count());
    }

    public function test_seeder_creates_projects(): void
    {
        $this->assertEquals(20, Project::count());
    }

    public function test_each_project_has_multiple_snapshots(): void
    {
        foreach (Project::all() as $project) {
            $this->assertGreaterThanOrEqual(5, $project->snapshots()->count(),
                "Project {$project->name} should have at least 5 snapshots");
            $this->assertLessThanOrEqual(8, $project->snapshots()->count(),
                "Project {$project->name} should have at most 8 snapshots");
        }
    }

    public function test_snapshots_are_backdated(): void
    {
        // At least one snapshot must be older than 2 weeks to prove backdating
        $oldSnapshots = ProjectSnapshot::where('created_at', '<', now()->subWeeks(2))->count();
        $this->assertGreaterThan(0, $oldSnapshots);
    }

    public function test_some_projects_have_comments(): void
    {
        // ~50% of 20 projects should have comments - loose bound
        $projectsWithComments = Project::has('comments')->count();
        $this->assertGreaterThan(0, $projectsWithComments);
        $this->assertGreaterThan(0, ProjectComment::count());
    }

    public function test_notifications_are_seeded(): void
    {
        $this->assertGreaterThan(0, DB::table('notifications')->count());
    }

    public function test_at_risk_projects_generate_notifications(): void
    {
        $atRisk = Project::whereIn('overall_health', ['at_risk', 'off_track'])->get();

        if ($atRisk->isEmpty()) {
            $this->markTestSkipped('No at_risk/off_track projects seeded');
        }

        // At least one user should have a seeded notification
        $userIds = $atRisk->pluck('created_by')->unique();
        $notificationCount = DB::table('notifications')
            ->whereIn('notifiable_id', $userIds)
            ->where('notifiable_type', User::class)
            ->count();

        $this->assertGreaterThan(0, $notificationCount);
    }

    public function test_notification_data_is_valid_json(): void
    {
        foreach (DB::table('notifications')->get() as $n) {
            $data = json_decode($n->data, true);
            $this->assertIsArray($data);
            $this->assertArrayHasKey('type', $data);
            $this->assertArrayHasKey('project_id', $data);
            $this->assertArrayHasKey('message', $data);
        }
    }

    public function test_snapshots_have_varied_spent_hours(): void
    {
        // Proof of mutation: snapshots per project should show varying spent_hours over time
        $project = Project::has('snapshots', '>=', 3)->first();
        $this->assertNotNull($project);

        $spentValues = $project->snapshots->pluck('snapshot_data')
            ->map(fn ($data) => $data['spent_hours'] ?? null)
            ->unique()
            ->values();

        $this->assertGreaterThan(1, $spentValues->count(),
            'Snapshots should show varied spent_hours across history');
    }
}
