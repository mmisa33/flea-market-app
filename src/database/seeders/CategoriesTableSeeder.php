<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::insert(
            [
                ['name' => 'ファッション'],
                ['name' => '家電'],
                ['name' => 'インテリア'],
                ['name' => 'レディース'],
                ['name' => 'メンズ'],
                ['name' => 'コスメ'],
                ['name' => '本'],
                ['name' => 'ゲーム'],
                ['name' => 'スポーツ'],
                ['name' => 'キッチン'],
                ['name' => 'ハンドメイド'],
                ['name' => 'アクセサリー'],
                ['name' => 'おもちゃ'],
                ['name' => 'ベビー・キッズ'],
            ]
        );
    }
}
