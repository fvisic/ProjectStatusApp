<?php

namespace Database\Factories;

use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'updated_by' => null,
            'name' => fake()->company() . ' ' . fake()->randomElement(['ERP', 'CRM', 'Migration', 'Integration']),
            'client' => fake()->company(),
            'team_lead' => fake()->name(),
            'report_date' => now(),
            'project_start' => now()->subMonths(2),
            'planned_go_live' => now()->addMonths(3),
            'project_type_id' => ProjectType::factory(),
            'current_phase' => fake()->randomElement([
                'instalacija_analiza', 'funkcionalna_specifikacija',
                'implementacija_testiranje', 'integracije',
                'uat_edukacija', 'go_live', 'hypercare',
            ]),
            'overall_health' => fake()->randomElement(['on_track', 'at_risk', 'off_track']),
            'estimated_hours' => fake()->numberBetween(100, 500),
            'spent_hours' => fake()->numberBetween(50, 300),
            'remaining_hours' => fake()->numberBetween(10, 200),
            'estimation_comment' => fake()->optional()->sentence(),
            'filled_by' => fake()->name(),
            'reviewed_by' => fake()->name(),
            'version' => 'v1.0',
        ];
    }
}
