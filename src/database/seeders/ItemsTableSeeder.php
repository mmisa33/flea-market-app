<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ItemLike;
use App\Models\Address;
use App\Models\Purchase;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        // カテゴリを取得
        $categories = Category::pluck('id', 'name')->toArray();

        // 商品データを作成
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

        // 定義した商品データを1件ずつ処理
        foreach ($items as $data) {
            $item = Item::create([
                'user_id' => $data['user_id'],
                'image_path' => $data['image_path'],
                'name' => $data['name'],
                'brand' => $data['brand'],
                'price' => $data['price'],
                'description' => $data['description'],
                'condition' => $data['condition'],
                'sold_status' => false, // 初期状態では未販売
            ]);

            // 商品にカテゴリを紐づけ
            $categoryIds = array_map(fn($name) => $categories[$name], $data['categories']);
            $item->categories()->attach($categoryIds);

            // 商品が出品された後に、ランダムでコメント、いいね、購入を処理
            $users = User::all();
            $otherUsers = $users->where('id', '!=', $data['user_id']); // 自分が出品した商品を除外

            // ランダムで2人のユーザーがコメント、いいね、購入を行う
            $randomUsers = $otherUsers->random(2);

            foreach ($randomUsers as $user) {
                // コメント
                Comment::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'content' => "コメント: {$item->name} に関するコメント",
                ]);

                // いいね
                ItemLike::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                ]);

                // 住所を取得・作成
                $address = Address::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'postal_code' => $user->profile->postal_code ?? '000-0000',
                        'address' => $user->profile->address ?? '仮住所',
                        'building' => $user->profile->building ?? '',
                    ]
                );

                // 50%の確率で購入を実施
                if (rand(0, 1) === 1) {
                    Purchase::create([
                        'user_id' => $user->id,
                        'item_id' => $item->id,
                        'address_id' => $address->id,
                        'payment_method' => 'card', // カード支払い
                    ]);

                    // 購入後は商品を売却状態に変更
                    $item->update(['sold_status' => true]);
                }
            }
        }
    }
}