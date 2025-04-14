<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // カテゴリを取得
        $fashionCategory = Category::where('name', 'ファッション')->first();
        $electronicsCategory = Category::where('name', '家電')->first();
        $interiorCategory = Category::where('name', 'インテリア')->first();
        $ladiesCategory = Category::where('name', 'レディース')->first();
        $mensCategory = Category::where('name', 'メンズ')->first();
        $cosmeticsCategory = Category::where('name', 'コスメ')->first();
        $bookCategory = Category::where('name', '本')->first();
        $gameCategory = Category::where('name', 'ゲーム')->first();
        $sportsCategory = Category::where('name', 'スポーツ')->first();
        $kitchenCategory = Category::where('name', 'キッチン')->first();
        $handmadeCategory = Category::where('name', 'ハンドメイド')->first();
        $accessoryCategory = Category::where('name', 'アクセサリー')->first();
        $toyCategory = Category::where('name', 'おもちゃ')->first();
        $babyKidsCategory = Category::where('name', 'ベビー・キッズ')->first();

        // アイテムを作成し、複数カテゴリを関連付け
        $item1 = Item::create([
            'user_id' => 1,
            'image_path' => 'images/items/Armani_Mens_Clock.jpg',
            'name' => '腕時計',
            'brand' => 'ブランド1',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => 'good',
            'sold_status' => false,
        ]);
        $item1->categories()->attach([$fashionCategory->id, $mensCategory->id]);

        $item2 = Item::create([
            'user_id' => 2,
            'image_path' => 'images/items/HDD_Hard_Disk.jpg',
            'name' => 'HDD',
            'brand' => 'ブランド2',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'condition' => 'no_damage',
            'sold_status' => false,
        ]);
        $item2->categories()->attach([$electronicsCategory->id]);

        $item3 = Item::create([
            'user_id' => 1,
            'image_path' => 'images/items/iLoveIMG_d.jpg',
            'name' => '玉ねぎ3束',
            'brand' => 'ブランド3',
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'condition' => 'some_damage',
            'sold_status' => false,
        ]);
        $item3->categories()->attach([$kitchenCategory->id]);

        $item4 = Item::create([
            'user_id' => 2,
            'image_path' => 'images/items/Leather_Shoes_Product_Photo.jpg',
            'name' => '革靴',
            'brand' => 'ブランド4',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'condition' => 'bad',
            'sold_status' => false,
        ]);
        $item4->categories()->attach([$fashionCategory->id, $mensCategory->id]);

        $item5 = Item::create([
            'user_id' => 1,
            'image_path' => 'images/items/Living_Room_Laptop.jpg',
            'name' => 'ノートPC',
            'brand' => 'ブランド5',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'condition' => 'good',
            'sold_status' => false,
        ]);
        $item5->categories()->attach([$electronicsCategory->id]);

        $item6 = Item::create([
            'user_id' => 2,
            'image_path' => 'images/items/Music_Mic_4632231.jpg',
            'name' => 'マイク',
            'brand' => 'ブランド6',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'condition' => 'no_damage',
            'sold_status' => false,
        ]);
        $item6->categories()->attach([$electronicsCategory->id]);

        $item7 = Item::create([
            'user_id' => 1,
            'image_path' => 'images/items/Purse_fashion_pocket.jpg',
            'name' => 'ショルダーバッグ',
            'brand' => 'ブランド7',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'condition' => 'some_damage',
            'sold_status' => false,
        ]);
        $item7->categories()->attach([$fashionCategory->id, $accessoryCategory->id]);

        $item8 = Item::create([
            'user_id' => 2,
            'image_path' => 'images/items/Tumbler_souvenir.jpg',
            'name' => 'タンブラー',
            'brand' => 'ブランド8',
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'condition' => 'bad',
            'sold_status' => false,
        ]);
        $item8->categories()->attach([$kitchenCategory->id]);

        $item9 = Item::create([
            'user_id' => 1,
            'image_path' => 'images/items/Waitress_with_Coffee_Grinder.jpg',
            'name' => 'コーヒーミル',
            'brand' => 'ブランド9',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'condition' => 'good',
            'sold_status' => false,
        ]);
        $item9->categories()->attach([$kitchenCategory->id]);

        $item10 = Item::create([
            'user_id' => 2,
            'image_path' => 'images/items/Makeup_Set.jpg',
            'name' => 'メイクセット',
            'brand' => 'ブランド10',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'condition' => 'no_damage',
            'sold_status' => false,
        ]);
        $item10->categories()->attach([$cosmeticsCategory->id]);
    }
}