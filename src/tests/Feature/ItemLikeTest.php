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
        $user = User::factory()->create();

        // プロフィール作成
        $user->profile()->create([
            'profile_image' => 'dummy.png',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル101'
        ]);

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('item.like', ['item' => $item->id]));

        $response->assertRedirect();
        $this->assertDatabaseHas('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    // いいね済みの商品は色付きのアイコンが表示される
    public function liked_items_display_colored_icon()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'dummy.png',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル301'
        ]);

        $item = Item::factory()->create();

        DB::table('item_likes')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('item.show', ['item_id' => $item->id]));

        $response->assertSee('images/icons/yellow_star_icon.png');
    }

    /** @test */
    // もう一度いいねアイコンを押すといいねが解除される
    public function clicking_like_icon_again_toggles_item_like()
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'profile_image' => 'dummy.png',
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
            'building' => '永田町ビル203'
        ]);

        $item = Item::factory()->create();

        // いいねする
        $this->actingAs($user)
            ->post(route('item.like', ['item' => $item->id]));

        // 再度押して解除
        $this->actingAs($user)
            ->post(route('item.like', ['item' => $item->id]));

        $this->assertDatabaseMissing('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
