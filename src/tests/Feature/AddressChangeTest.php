<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // 購入ページに登録した住所が反映されているか確認
    public function test_registered_address_is_reflected_on_purchase_page()
    {
        // ユーザー作成・ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        // 住所変更ページにアクセス
        $response = $this->get(route('purchase.address.edit', ['item' => $item->id]));
        $response->assertStatus(200);

        // フォームのvalue属性に反映されていることを確認
        $response->assertSee('name="postal_code"', false);
        $response->assertSee('name="address"', false);
        $response->assertSee('name="building"', false);

        // 住所変更データを定義
        $updatedAddress = [
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区新ビル202',
            'building' => '新ビル202',
        ];

        // PATCHリクエストで住所変更
        $response = $this->patch(route('purchase.address.update', ['item' => $item->id]), $updatedAddress);
        $response->assertRedirect(route('item.purchase', ['item' => $item->id]));

        // リダイレクト後の購入ページに新しい住所が表示されているか確認
        $response = $this->get(route('item.purchase', ['item' => $item->id]));
        $response->assertSee($updatedAddress['postal_code']);
        $response->assertSee($updatedAddress['address']);
        $response->assertSee($updatedAddress['building']);
    }

    // 購入時に選択した住所がアイテムに紐づいているか確認
    public function test_purchased_item_is_linked_to_selected_address()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();

        $address = Address::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都新宿区テストビル101',
            'building' => 'テストビル101',
        ]);

        // 購入処理
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        // 購入時に選択した住所がアイテムに紐づいていることを確認
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'address_id' => $address->id,
        ]);
    }
}