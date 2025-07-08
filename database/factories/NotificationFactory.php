<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'data' => [
                'title' => $this->faker->sentence(),
                'message' => $this->faker->paragraph(),
                'action_url' => $this->faker->url(),
                'icon' => $this->faker->randomElement(['info', 'success', 'warning', 'error']),
            ],
            'type' => $this->faker->randomElement([
                'project_created',
                'project_updated', 
                'task_assigned',
                'task_completed',
                'comment_added',
                'team_invitation',
                'deadline_reminder'
            ]),
            'read_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    /**
     * Create a project-related notification.
     */
    public function projectNotification(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $this->faker->randomElement(['project_created', 'project_updated']),
            'data' => [
                'title' => $this->faker->randomElement([
                    'New Project Created',
                    'Project Updated',
                    'Project Status Changed'
                ]),
                'message' => $this->faker->sentence(),
                'project_id' => $this->faker->numberBetween(1, 100),
                'action_url' => '/projects/' . $this->faker->numberBetween(1, 100),
            ],
        ]);
    }

    /**
     * Create a task-related notification.
     */
    public function taskNotification(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $this->faker->randomElement(['task_assigned', 'task_completed']),
            'data' => [
                'title' => $this->faker->randomElement([
                    'Task Assigned to You',
                    'Task Completed',
                    'Task Deadline Approaching'
                ]),
                'message' => $this->faker->sentence(),
                'task_id' => $this->faker->numberBetween(1, 100),
                'action_url' => '/tasks/' . $this->faker->numberBetween(1, 100),
            ],
        ]);
    }
}
