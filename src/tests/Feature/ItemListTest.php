<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // 全商品が表示されるか
    public function all_items_are_displayed()
    {
        $item1 = Item::factory()->create(['sold_status' => false]); // 未購入商品
        $item2 = Item::factory()->create(['sold_status' => true]);  // 購入済み商品

        // 商品ページにアクセス
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($item1->name); // 商品1が表示される
        $response->assertSee($item2->name); // 商品2が表示される
    }

    /** @test */
    // 購入済み商品に「Sold」のラベルが表示されるか
    public function sold_items_display_sold_label()
    {
        $item = Item::factory()->create(['sold_status' => true]);

        // 商品ページにアクセス
        $response = $this->get('/');

        $response->assertSee('Sold'); // 購入済み商品に「Sold」ラベルが表示される
    }

    /** @test */
    // 自分が出品した商品が表示されないか
    public function items_sold_by_the_user_are_not_displayed()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]); // 自分が出品した商品

        $this->actingAs($user); // ログイン

        // 商品ページにアクセス
        $response = $this->get('/');

        $response->assertDontSee($item->name); // 自分が出品した商品は表示されない
    }
}
