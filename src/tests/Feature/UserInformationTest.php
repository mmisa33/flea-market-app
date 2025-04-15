<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInformationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // ユーザー情報がプロフィールページに表示されている
    public function user_information_is_displayed_on_profile_page()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        // 出品した商品を作成
        $item = Item::factory()->create(['user_id' => $user->id]);

        // 購入した商品を作成
        $purchase = Purchase::factory()->create(['user_id' => $user->id, 'item_id' => $item->id]);

        // 出品した商品が表示されるべきページにアクセス
        $response = $this->get(route('profile.show', ['page' => 'sell']));

        // ユーザーのプロフィール画像、ユーザー名、出品した商品、購入した商品が表示されることを確認
        $response->assertSee($user->profile->profile_image);
        $response->assertSee($user->name);
        $response->assertSee($item->name);
        $response->assertSee($purchase->item->name);
    }
}