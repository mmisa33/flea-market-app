<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 商品データ登録
        $items = [
            [
                'user_id' => 1,
                'image_path' => 'images/items/Armani_Mens_Clock.jpg',
                'name' => '腕時計',
                'brand' => 'ブランド1',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => 'good',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'user_id' => 2,
                'image_path' => 'images/items/HDD_Hard_Disk.jpg',
                'name' => 'HDD',
                'brand' => 'ブランド2',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => 'no_damage',
                'categories' => ['家電'],
            ],
            [
                'user_id' => 3,
                'image_path' => 'images/items/iLoveIMG_d.jpg',
                'name' => '玉ねぎ3束',
                'brand' => 'ブランド3',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => 'some_damage',
                'categories' => ['キッチン'],
            ],
            [
                'user_id' => 1,
                'image_path' => 'images/items/Leather_Shoes_Product_Photo.jpg',
                'name' => '革靴',
                'brand' => 'ブランド4',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'condition' => 'bad',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'user_id' => 2,
                'image_path' => 'images/items/Living_Room_Laptop.jpg',
                'name' => 'ノートPC',
                'brand' => 'ブランド5',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'condition' => 'good',
                'categories' => ['家電'],
            ],
            [
                'user_id' => 3,
                'image_path' => 'images/items/Music_Mic_4632231.jpg',
                'name' => 'マイク',
                'brand' => 'ブランド6',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'condition' => 'no_damage',
                'categories' => ['家電'],
            ],
            [
                'user_id' => 1,
                'image_path' => 'images/items/Purse_fashion_pocket.jpg',
                'name' => 'ショルダーバッグ',
                'brand' => 'ブランド7',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => 'some_damage',
                'categories' => ['ファッション', 'アクセサリー'],
            ],
            [
                'user_id' => 2,
                'image_path' => 'images/items/Tumbler_souvenir.jpg',
                'name' => 'タンブラー',
                'brand' => 'ブランド8',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'condition' => 'bad',
                'categories' => ['キッチン'],
            ],
            [
                'user_id' => 3,
                'image_path' => 'images/items/Waitress_with_Coffee_Grinder.jpg',
                'name' => 'コーヒーミル',
                'brand' => 'ブランド9',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'condition' => 'good',
                'categories' => ['キッチン'],
            ],
            [
                'user_id' => 1,
                'image_path' => 'images/items/Makeup_Set.jpg',
                'name' => 'メイクセット',
                'brand' => 'ブランド10',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'condition' => 'no_damage',
                'categories' => ['コスメ'],
            ],
        ];

        foreach ($items as $item) {
            $createdItem = Item::create([
                'user_id' => $item['user_id'],
                'image_path' => $item['image_path'],
                'name' => $item['name'],
                'brand' => $item['brand'],
                'price' => $item['price'],
                'description' => $item['description'],
                'condition' => $item['condition'],
                'sold_status' => false,
            ]);

            $createdItem->categories()->attach(
                \App\Models\Category::whereIn('name', $item['categories'])->pluck('id')->toArray()
            );
        }
    }
}