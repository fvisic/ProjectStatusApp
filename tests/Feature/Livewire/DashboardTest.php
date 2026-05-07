<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_dashboard_redirects_guests(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_empty_state_without_projects(): void
    {
        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee(__('dashboard.welcome'))
            ->assertSee(__('projects.new_project'));
    }

    public function test_dashboard_shows_kpi_cards_with_projects(): void
    {
        Project::factory()->count(3)->create([
            'created_by' => $this->user->id,
            'overall_health' => 'on_track',
            'estimated_hours' => 200,
            'spent_hours' => 100,
            'remaining_hours' => 80,
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee(__('dashboard.total_projects'))
            ->assertSee(__('dashboard.health_overview'))
            ->assertSee(__('dashboard.estimation_total'))
            ->assertSee(__('dashboard.forecast_delta'));
    }

    public function test_dashboard_shows_alert_for_at_risk_projects(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Risky Project',
            'overall_health' => 'at_risk',
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Risky Project');
    }

    public function test_dashboard_shows_overdue_go_lives(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Late Project',
            'planned_go_live' => now()->subDays(5),
            'current_phase' => 'uat_edukacija',
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Late Project')
            ->assertSee(__('dashboard.overdue'));
    }

    public function test_dashboard_shows_upcoming_go_lives(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Launching Soon',
            'planned_go_live' => now()->addDays(10),
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Launching Soon');
    }

    public function test_dashboard_does_not_show_other_users_data(): void
    {
        $otherUser = User::factory()->create();
        Project::factory()->create([
            'created_by' => $otherUser->id,
            'name' => 'Other Project',
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertDontSee('Other Project')
            ->assertSee(__('dashboard.welcome'));
    }

    public function test_dashboard_shows_estimation_overrun(): void
    {
        Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Overrun Project',
            'estimated_hours' => 100,
            'spent_hours' => 90,
            'remaining_hours' => 50,
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Overrun Project');
    }

    public function test_dashboard_shows_recent_activity(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Activity Project',
        ]);
        $project->createSnapshot($this->user->id, 'Initial save');

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Activity Project')
            ->assertSee(__('dashboard.recent_activity'));
    }
}
