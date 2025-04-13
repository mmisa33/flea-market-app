<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'image_path' => $this->faker->imageUrl(),
            'name' => $this->faker->word,
            'brand' => $this->faker->company,
            'price' => $this->faker->numberBetween(1, 10000),
            'description' => $this->faker->sentence,
            'condition' => $this->faker->numberBetween(1, 4),
            'sold_status' => $this->faker->boolean,
        ];
    }
}