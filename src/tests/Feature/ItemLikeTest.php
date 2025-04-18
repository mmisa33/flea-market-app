<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // ユーザーはいいねアイコンを押すことで商品にいいねできる
    public function user_can_like_item_by_clicking_like_icon()
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

        // 「いいね」アイコンをクリックしていいねを付ける
        $response = $this->actingAs($user)
            ->post(route('item.like', ['item' => $item->id]));

        // いいね後にリダイレクトを確認
        $response->assertRedirect();

        // item_likesテーブルにレコードが作成されていることを確認
        $this->assertDatabaseHas('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    // いいね済みの商品は色付きのアイコンが表示される
    public function liked_items_display_colored_icon()
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

        // 「いいね」アイコンをクリックしていいねを付ける
        $this->actingAs($user)->post(route('item.like', ['item' => $item->id]));

        // 商品詳細ページにアクセスし、色付きアイコンが表示されていることを確認
        $response = $this->actingAs($user)
            ->get(route('item.show', ['item_id' => $item->id]));

        $response->assertSee('images/icons/yellow_star_icon.png');
    }

    /** @test */
    // もう一度いいねアイコンを押すといいねが解除される
    public function clicking_like_icon_again_toggles_item_like()
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

        // 「いいね」アイコンをクリックしていいねを付ける
        $this->actingAs($user)
            ->post(route('item.like', ['item' => $item->id]));

        // 再度押していいねを解除
        $this->actingAs($user)
            ->post(route('item.like', ['item' => $item->id]));

        $this->assertDatabaseMissing('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
