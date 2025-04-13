<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // 商品名で部分一致検索ができる
    public function item_name_partial_search_is_possible()
    {
        // 商品を作成
        $item1 = Item::factory()->create(['name' => '腕時計']);
        $item2 = Item::factory()->create(['name' => 'バッグ']);

        // 検索キーワード「腕」
        $response = $this->get('/?keyword=腕'); // 検索欄に「腕」を入力して検索

        // 部分一致する商品が表示される
        $response->assertSee($item1->name);
        $response->assertDontSee($item2->name);
    }

    /** @test */
    // 検索状態がマイリストでも保持されている
    public function search_keyword_is_preserved_in_my_list()
    {
        // 商品を作成
        $item1 = Item::factory()->create(['name' => '腕時計']);
        $item2 = Item::factory()->create(['name' => 'バッグ']);

        // 検索キーワード「腕」
        $response = $this->get('/?keyword=腕'); // ホームページで検索

        // 検索結果が表示される
        $response->assertSee($item1->name);
        $response->assertDontSee($item2->name);

        // マイリストページに遷移
        $response = $this->get('/?page=mylist&keyword=腕'); // マイリストページに遷移

        // 検索キーワードが保持されている
        $response->assertSee('腕');
    }
}