<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // 商品出品画面にて必要な情報が保存できる
    public function item_registration_saves_correctly()
    {
        Storage::fake('public');

        // ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->profile()->create([
            'profile_image' => 'test.jpg',
            'postal_code'   => '123-4567',
            'address'       => '東京都新宿区',
            'building'      => 'テストビル101',
        ]);

        // カテゴリ作成
        $category = Category::factory()->create();

        // ダミー画像生成
        $fakeImage = UploadedFile::fake()->create('test_image.jpg', 100, 'image/jpeg');

        // 出品フォームの入力データ
        $itemData = [
            'name'        => 'テスト商品',
            'brand'       => 'テストブランド',
            'description' => 'テスト商品説明',
            'price'       => 1500,
            'condition'   => '新品',
            'category'    => $category->id,
            'image_path'  => $fakeImage,
        ];

        $this->get(route('item.create'));

        // 出品処理（POSTリクエスト送信）
        $response = $this->post(route('item.store'), $itemData);

        // データベースに商品情報が保存されていることを確認
        $this->assertDatabaseHas('items', [
            'user_id'    => $user->id,
            'name'       => 'テスト商品',
            'brand'      => 'テストブランド',
            'description' => 'テスト商品説明',
            'price'      => 1500,
            'condition'  => '新品',
            'sold_status' => false,
        ]);

        // カテゴリが正しく紐づけられていることを確認
        $item = Item::firstWhere('name', 'テスト商品');
        $this->assertTrue($item->categories->contains($category));

        // 画像ファイルが保存されていることを確認
        Storage::disk('public')->assertExists('images/items/' . $fakeImage->hashName());

        // 出品後マイページにリダイレクトされることを確認
        $response->assertRedirect(route('profile.show'));
    }
}