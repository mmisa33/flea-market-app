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
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'sold_status' => false, // 未販売
        ]);

        // 住所を作成
        $address = Address::factory()->create([
            'user_id' => $user->id, // ユーザーの住所
        ]);

        // 購入処理
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        // 購入がデータベースに保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    // 購入した商品は商品一覧画面にて「sold」と表示される
    public function test_purchased_item_shows_sold_in_list()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);
        $this->post(route('item.purchase.submit', $item->id));

        $response = $this->get('/');
        $response->assertSee('Sold');
    }

    /** @test */
    // 購入した商品が「プロフィール/購入した商品一覧」に追加されている
    public function purchased_item_appears_in_profile_purchase_list()
    {
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
        $this->assertTrue($user->purchase->contains($purchase));
    }
}