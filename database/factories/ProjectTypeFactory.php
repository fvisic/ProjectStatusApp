<?php

namespace Database\Factories;

use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectTypeFactory extends Factory
{
    protected $model = ProjectType::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->words(2, true) . ' type',
            'color'      => fake()->randomElement(array_keys(ProjectType::$colors)),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
