<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // ログアウト処理が正しく動作するか
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // ログアウト処理
        $response = $this->post('/logout');

        // ログアウト後はゲスト状態
        $this->assertGuest();

        // ログアウト後、トップページにリダイレクト
        $response->assertRedirect('/');
    }
}
