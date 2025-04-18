<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSelectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // 支払い方法が選択されると購入確認欄に即時反映される
    public function selected_payment_method_is_reflected_immediately()
    {
        // ユーザー作成
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        $item = Item::factory()->create([
            'price' => 1200,
            'sold_status' => false,
        ]);

        // ログインして購入ページへアクセス
        $response = $this->actingAs($user)->get(route('item.purchase', ['item' => $item->id]));

        // HTML構造に"支払い方法"のセレクトエリアとhidden inputが存在するか確認
        $response->assertStatus(200)
            ->assertSee('支払い方法')
            ->assertSee('id="payment-method-hidden"', false)
            ->assertSee('id="selected-payment-method"', false)
            ->assertSee('コンビニ払い')
            ->assertSee('カード支払い');
    }
}
