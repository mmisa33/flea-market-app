<?php

use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // 「購入する」ボタンを押下すると購入が完了する
    public function user_can_purchase_item()
    {
        // ユーザー作成
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'sold_status' => false, // 未販売
        ]);

        // 住所を作成
        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        // 購入処理
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        // 購入がデータベースに保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);
    }

    /** @test */
    // 購入した商品は商品一覧画面にて「sold」と表示される
    public function purchased_item_shows_sold_status_in_item_list()
    {
        // ユーザー作成
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        // 住所セッションを設定
        session(["shippingAddress_{$item->id}" => [
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]]);

        // 購入処理
        $this->post(route('item.purchase.submit', $item->id), [
            'payment_method' => 'card',
        ]);

        // 商品一覧画面に「Sold」が表示されることを確認
        $response = $this->get('/');
        $response->assertSee('Sold');
    }

    /** @test */
    // 購入した商品が「プロフィール/購入した商品一覧」に追加されている
    public function purchased_item_appears_in_profile_purchase_list()
    {
        // ユーザー作成
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'sold_status' => false,
        ]);
        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        // ユーザーの購入履歴に購入アイテムが表示されることを確認
        $user->load('purchases');
        $this->assertTrue($user->purchases->contains($purchase));
    }
}