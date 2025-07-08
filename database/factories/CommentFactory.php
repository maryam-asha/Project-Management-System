<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'content' => $this->faker->sentence,
            'user_id' => User::factory(),
            'commentable_id' => 1,
            'commentable_type' => 'App\\Models\\Task',
        ];
    }
}
