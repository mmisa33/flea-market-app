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
        // 商品状態のリスト
        $conditions = [
            'good' => '良好',
            'no_damage' => '目立った傷や汚れなし',
            'some_damage' => 'やや傷や汚れあり',
            'bad' => '状態が悪い',
        ];

        return [
            'user_id' => User::factory(),
            'image_path' => $this->faker->imageUrl(),
            'name' => $this->faker->word,
            'brand' => $this->faker->company,
            'price' => $this->faker->numberBetween(1, 10000),
            'description' => $this->faker->sentence,
            'condition' => $this->faker->randomElement(array_keys($conditions)),
            'sold_status' => $this->faker->boolean,
        ];
    }
}