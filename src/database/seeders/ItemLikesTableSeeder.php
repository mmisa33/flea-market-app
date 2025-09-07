<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemLike;

class ItemLikesTableSeeder extends Seeder
{
    public function run(): void
    {
        $likes = [
            // ユーザーID1のいいね商品
            ['user_id' => 1, 'item_id' => 6],
            ['user_id' => 1, 'item_id' => 7],

            // ユーザーID2のいいね商品
            ['user_id' => 2, 'item_id' => 2],
            ['user_id' => 2, 'item_id' => 3],

            // ユーザーID3のいいね商品
            ['user_id' => 3, 'item_id' => 1],
            ['user_id' => 3, 'item_id' => 4],
        ];

        foreach ($likes as $like) {
            ItemLike::create($like);
        }
    }
}