<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;


class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::create([
            'user_id' => 1,
            'image_path' => 'items/Armani_Mens_Clock.jpg',
            'name' => '腕時計',
            'brand' => null,
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => 1, // 良好
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 2,
            'image_path' => 'items/HDD_Hard_Disk.jpg',
            'name' => 'HDD',
            'brand' => null,
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'condition' => 2, // 目立った傷や汚れなし
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 1,
            'image_path' => 'items/iLoveIMG_d.jpg',
            'name' => '玉ねぎ3束',
            'brand' => null,
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'condition' => 3, // やや傷や汚れあり
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 2,
            'image_path' => 'items/Leather_Shoes_Product_Photo.jpg',
            'name' => '革靴',
            'brand' => null,
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'condition' => 4, // 状態が悪い
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 1,
            'image_path' => 'items/Living_Room_Laptop.jpg',
            'name' => 'ノートPC',
            'brand' => null,
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'condition' => 1, // 良好
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 2,
            'image_path' => 'items/Music_Mic_4632231.jpg',
            'name' => 'マイク',
            'brand' => null,
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'condition' => 2, // 目立った傷や汚れなし
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 1,
            'image_path' => 'items/Purse_fashion_pocket.jpg',
            'name' => 'ショルダーバッグ',
            'brand' => null,
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'condition' => 3, // やや傷や汚れあり
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 2,
            'image_path' => 'items/Tumbler_souvenir.jpg',
            'name' => 'タンブラー',
            'brand' => null,
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'condition' => 4, // 状態が悪い
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 1,
            'image_path' => 'items/Waitress_with_Coffee_Grinder.jpg',
            'name' => 'コーヒーミル',
            'brand' => null,
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'condition' => 1, // 良好
            'sold_status' => false,
        ]);

        Item::create([
            'user_id' => 2,
            'image_path' => 'items/Makeup_Set.jpg',
            'name' => 'メイクセット',
            'brand' => null,
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'condition' => 2, // 目立った傷や汚れなし
            'sold_status' => false,
        ]);
    }
}
