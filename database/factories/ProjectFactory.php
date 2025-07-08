<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['active', 'pending', 'completed']),
            'due_date' => $this->faker->date(),
            'team_id' => Team::factory(),
            'created_by_user_id' => User::factory(),
        ];
    }
}


// public function pending(): static
// {
//     return $this->state(fn (array $attributes) => [
//         'status' => 'pending',
//     ]);
// }

// Create a pending project
// Project::factory()->pending()->create();
