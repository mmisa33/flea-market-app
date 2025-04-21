<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Address;

class PurchasesTableSeeder extends Seeder
{
    public function run(): void
    {
        // ユーザーのプロフィールから住所情報を流用し、addressesテーブルに登録
        $address1 = Address::create([
            'user_id' => 1,
            'postal_code' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => '千代田ビル',
        ]);

        $address2 = Address::create([
            'user_id' => 2,
            'postal_code' => '150-0001',
            'address' => '東京都渋谷区渋谷1-2-3',
            'building' => '渋谷ビル',
        ]);

        $address3 = Address::create([
            'user_id' => 3,
            'postal_code' => '160-0004',
            'address' => '東京都新宿区新宿1-1-1',
            'building' => '新宿ビル',
        ]);

        // 購入データ
        $purchases = [
            ['user_id' => 1, 'item_id' => 2, 'address_id' => $address1->id, 'payment_method' => 'card'],
            ['user_id' => 1, 'item_id' => 3, 'address_id' => $address1->id, 'payment_method' => 'konbini'],

            ['user_id' => 2, 'item_id' => 6, 'address_id' => $address2->id, 'payment_method' => 'konbini'],
            ['user_id' => 2, 'item_id' => 7, 'address_id' => $address2->id, 'payment_method' => 'card'],

            ['user_id' => 3, 'item_id' => 10, 'address_id' => $address3->id, 'payment_method' => 'konbini'],
        ];

        // 購入登録とアイテムのsold_status更新
        foreach ($purchases as $purchase) {
            Purchase::create($purchase);

            $item = Item::find($purchase['item_id']);
            $item->sold_status = true;
            $item->save();
        }
    }
}