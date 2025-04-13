<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->hasProfile(),
            'item_id' => Item::factory(),
            'content' => $this->faker->sentence(),
        ];
    }
}