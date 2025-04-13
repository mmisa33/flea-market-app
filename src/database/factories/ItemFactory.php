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
            'user_id' => User::factory(),  // Userモデルのファクトリを使ってユーザーIDを設定
            'image_path' => $this->faker->imageUrl(),  // ダミーの画像URLを生成
            'name' => $this->faker->word,  // 商品名
            'brand' => $this->faker->company,  // ブランド名
            'price' => $this->faker->numberBetween(1000, 10000),  // 価格
            'description' => $this->faker->sentence,  // 商品説明
            'condition' => $this->faker->numberBetween(1, 5),  // 商品の状態
            'sold_status' => $this->faker->boolean,  // 売却済みかどうか
        ];
    }
}