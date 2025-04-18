<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // 必要な商品情報が表示される
    public function test_item_details_are_displayed_correctly()
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

        // コメント作成
        Comment::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);

        // カテゴリ作成
        $categories = Category::factory()->count(1)->create();
        $item->categories()->attach($categories->pluck('id'));

        // 商品状態のラベルを取得
        $conditions = [
            'good' => '良好',
            'no_damage' => '目立った傷や汚れなし',
            'some_damage' => 'やや傷や汚れあり',
            'bad' => '状態が悪い'
        ];
        $conditionLabel = $conditions[$item->condition];

        // 商品詳細ページにアクセス
        $response = $this->get("/item/{$item->id}");

        // 商品詳細情報が表示されているか確認
        $response->assertSee($item->image_url); // 商品画像
        $response->assertSee($item->name); // 商品名
        $response->assertSee($item->brand); // ブランド名
        $response->assertSee(number_format($item->price)); // 価格
        $response->assertSee($item->like_count); // いいね数
        $response->assertSee($item->comment_count); // コメント数
        $response->assertSee($item->description); // 商品説明
        $response->assertSee($categories[0]->name); // カテゴリ
        $response->assertSee($conditionLabel); // 商品状態
        $response->assertSee($user->name); // コメントしたユーザー名
        $response->assertSee($user->profile->profile_image);  // コメントしたユーザーのプロフィール画像
        $response->assertSee('テストコメント'); // コメント内容
    }

    /** @test */
    // 複数選択されたカテゴリが表示される
    public function test_item_details_are_displayed_correctly_with_multiple_categories()
    {
        // 複数カテゴリを作成
        $category1 = Category::create(['name' => '洋服']);
        $category2 = Category::create(['name' => 'メンズ']);

        // 商品を作成し、複数カテゴリを関連付ける
        $item = Item::factory()->create();
        $item->categories()->attach([$category1->id, $category2->id]);

        // 商品詳細ページにアクセス
        $response = $this->get("/item/{$item->id}");

        // 複数カテゴリが表示されていることを確認
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
    }
}