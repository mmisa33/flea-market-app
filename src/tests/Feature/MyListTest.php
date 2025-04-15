<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ItemLike;
use App\Models\Address;
use App\Models\Purchase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // マイリストにはいいねした商品だけが表示される
    public function my_list_displays_liked_items_only()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // いいねした商品を作成し、マイリストに追加
        $likedItem = Item::factory()->create();
        $user->likedItems()->attach($likedItem->id);

        // マイリストにアクセス
        $response = $this->get('/?page=mylist');

        // いいねした商品が表示されることを確認
        $response->assertSee($likedItem->name);
    }

    /** @test */
    // マイリストの購入済み商品は「Sold」と表示される
    public function my_list_displays_sold_items_with_sold_label()
    {
        // ログインユーザーの作成
        $user = User::factory()->create();

        // 他人が出品した商品（購入済み）を作成
        $soldItem = Item::factory()->create([
            'sold_status' => true,
            'user_id' => User::factory()->create()->id,
        ]);

        // 購入者住所を作成
        $address = Address::factory()->create(['user_id' => $user->id]);

        // 購入情報を作成
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        // いいねに追加
        ItemLike::factory()->create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        // マイリストにアクセス
        $response = $this->actingAs($user)->get('/?page=mylist');

        // 商品名と「Sold」ラベルが表示されることを確認
        $response->assertSee($soldItem->name);
        $response->assertSee('Sold');
    }

    /** @test */
    // マイリストには自分が出品した商品は表示されない
    public function my_list_does_not_display_own_items()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 自分が出品した商品を作成
        $ownItem = Item::factory()->create(['user_id' => $user->id]);

        // マイリストにアクセス
        $response = $this->get('/?page=mylist');

        // 自分の商品が表示されないことを確認
        $response->assertDontSee($ownItem->name);
    }

    /** @test */
    // マイリストには未認証の場合は何も表示されない
    public function my_list_displays_nothing_for_unauthenticated_users()
    {
        // 未ログインの状態でマイリストにアクセス
        $response = $this->get('/?page=mylist');

        // 商品が何も表示されないことを確認
        $response->assertDontSee('item__card');

        // itemsが空でビューが正常に表示されていることも確認
        $response->assertStatus(200);
    }
}