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

        // 未ログイン状態でコメントを投稿
        $response = $this->post(route('item.comment', ['item_id' => $item->id]), [
            'content' => 'Great product!'
        ]);

        // ログイン画面にリダイレクトされることを確認
        $response->assertRedirect(route('login'));

        // コメントがデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'Great product!',
        ]);
    }

    /** @test */
    // コメントが入力されていない場合、バリデーションメッセージが表示される
    public function validation_error_when_comment_is_empty()
    {
        // ユーザー作成・ログイン
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        // 空のコメントを送信
        $response = $this->actingAs($user)
            ->post(route('item.comment', ['item_id' => $item->id]), [
                'content' => ''
            ]);

        // 'content' に対してエラーが発生することを確認
        $response->assertSessionHasErrors('content');

        // コメントがデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => ''
        ]);
    }

    /** @test */
    // コメントが255字以上の場合、バリデーションメッセージが表示される
    public function validation_error_when_comment_is_too_long()
    {
        // ユーザー作成・ログイン
        $user = User::factory()->create();
        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        $item = Item::factory()->create();

        // 256文字のコメントを作成
        $longComment = str_repeat('A', 256); // 256文字

        $response = $this->actingAs($user)
            ->post(route('item.comment', ['item_id' => $item->id]), [
                'content' => $longComment
            ]);

        // 'content' に対してエラーが発生することを確認
        $response->assertSessionHasErrors('content');

        // コメントがデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => $longComment
        ]);
    }
}