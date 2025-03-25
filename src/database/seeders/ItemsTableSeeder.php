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
            'image_path' => 'items/Armani_Mens_Clock.jpg',
            'name' => '腕時計',
            'brand' => null,
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => 1,
            'sold_status' => false,
        ]);
        $item1->categories()->attach([$fashionCategory->id, $mensCategory->id]); // ファッション、メンズ

        $item2 = Item::create([
            'user_id' => 2,
            'image_path' => 'items/HDD_Hard_Disk.jpg',
            'name' => 'HDD',
            'brand' => null,
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'condition' => 2,
            'sold_status' => false,
        ]);
        $item2->categories()->attach([$electronicsCategory->id]); // 家電

        $item3 = Item::create([
            'user_id' => 1,
            'image_path' => 'items/iLoveIMG_d.jpg',
            'name' => '玉ねぎ3束',
            'brand' => null,
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'condition' => 3,
            'sold_status' => false,
        ]);
        $item3->categories()->attach([$kitchenCategory->id]); // キッチン

        $item4 = Item::create([
            'user_id' => 2,
            'image_path' => 'items/Leather_Shoes_Product_Photo.jpg',
            'name' => '革靴',
            'brand' => null,
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'condition' => 4,
            'sold_status' => false,
        ]);
        $item4->categories()->attach([$fashionCategory->id, $mensCategory->id]); // ファッション、メンズ

        $item5 = Item::create([
            'user_id' => 1,
            'image_path' => 'items/Living_Room_Laptop.jpg',
            'name' => 'ノートPC',
            'brand' => null,
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'condition' => 1,
            'sold_status' => false,
        ]);
        $item5->categories()->attach([$electronicsCategory->id]); // 家電

        $item6 = Item::create([
            'user_id' => 2,
            'image_path' => 'items/Music_Mic_4632231.jpg',
            'name' => 'マイク',
            'brand' => null,
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'condition' => 2,
            'sold_status' => false,
        ]);
        $item6->categories()->attach([$electronicsCategory->id]); // 家電

        $item7 = Item::create([
            'user_id' => 1,
            'image_path' => 'items/Purse_fashion_pocket.jpg',
            'name' => 'ショルダーバッグ',
            'brand' => null,
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'condition' => 3,
            'sold_status' => false,
        ]);
        $item7->categories()->attach([$fashionCategory->id, $accessoryCategory->id]); // ファッション、アクセサリー

        $item8 = Item::create([
            'user_id' => 2,
            'image_path' => 'items/Tumbler_souvenir.jpg',
            'name' => 'タンブラー',
            'brand' => null,
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'condition' => 4,
            'sold_status' => false,
        ]);
        $item8->categories()->attach([$kitchenCategory->id]); // キッチン

        $item9 = Item::create([
            'user_id' => 1,
            'image_path' => 'items/Waitress_with_Coffee_Grinder.jpg',
            'name' => 'コーヒーミル',
            'brand' => null,
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'condition' => 1,
            'sold_status' => false,
        ]);
        $item9->categories()->attach([$kitchenCategory->id]); // キッチン

        $item10 = Item::create([
            'user_id' => 2,
            'image_path' => 'items/Makeup_Set.jpg',
            'name' => 'メイクセット',
            'brand' => null,
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'condition' => 2,
            'sold_status' => false,
        ]);
        $item10->categories()->attach([$cosmeticsCategory->id]); // コスメ
    }
}