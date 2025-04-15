<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInformationChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // ユーザー情報の各項目の初期値がプロフィール編集ページに正しく表示されている
    public function user_information_is_displayed_correctly_on_profile_page()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $this->actingAs($user);

        $profile = $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
        ]);

        // プロフィール編集ページにアクセス
        $response = $this->get(route('profile.edit'));

        // プロフィール画像、ユーザー名、郵便番号、住所の初期値が表示されている
        $response->assertSee($user->profile->profile_image);
        $response->assertSee($user->name);
        $response->assertSee($profile->postal_code);
        $response->assertSee($profile->address);
        $response->assertSee($profile->building);
    }
}