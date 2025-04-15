<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // ログイン済みのユーザーはコメントを送信できる
    public function test_logged_in_user_can_submit_comment()
    {
        // ユーザーとアイテムを作成
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'dummy.png',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル101'
        ]);
        $item = Item::factory()->create();

        // ユーザーをログイン状態にする
        $this->actingAs($user);

        // コメントを投稿
        $response = $this->post(route('item.comment', $item->id), [
            'content' => 'Great product!',
        ]);

        // 商品詳細ページにリダイレクトされていることを確認
        $response->assertRedirect(route('item.show', $item->id));

        // コメントがデータベースに保存されているか確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'Great product!',
        ]);
    }

    /** @test */
    // ログイン前のユーザーはコメントを送信できない
    public function guest_user_cannot_submit_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('item.comment', ['item_id' => $item->id]), [
            'content' => 'Great product!'
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'Great product!',
        ]);
    }

    /** @test */
    // コメントが入力されていない場合、バリデーションメッセージが表示される
    public function validation_error_when_comment_is_empty()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'dummy.png',
            'postal_code' => '123-4567',
            'address' => 'Shinjuku, Tokyo',
            'building' => 'Sample Building 101'
        ]);

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('item.comment', ['item_id' => $item->id]), [
                'content' => ''
            ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => ''
        ]);
    }

    /** @test */
    // コメントが255字以上の場合、バリデーションメッセージが表示される
    public function validation_error_when_comment_is_too_long()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'dummy.png',
            'postal_code' => '123-4567',
            'address' => 'Shinjuku, Tokyo',
            'building' => 'Sample Building 101'
        ]);

        $item = Item::factory()->create();

        $longComment = str_repeat('A', 256); // 256文字

        $response = $this->actingAs($user)
            ->post(route('item.comment', ['item_id' => $item->id]), [
                'content' => $longComment
            ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => $longComment
        ]);
    }
}