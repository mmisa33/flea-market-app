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

        $response = $this->post('/logout'); // ログアウト処理

        $this->assertGuest(); // ログアウト後はゲスト状態
        $response->assertRedirect('/'); // ログアウト後、トップページにリダイレクト
    }
}
