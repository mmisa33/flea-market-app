<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            // ユーザーID1のコメント
            ['user_id' => 1, 'item_id' => 6, 'content' => '高音質で良さそうですね！'],
            ['user_id' => 1, 'item_id' => 7, 'content' => 'おしゃれなバッグです。'],

            // ユーザーID2のコメント
            ['user_id' => 2, 'item_id' => 1, 'content' => 'かっこいい腕時計ですね！'],
            ['user_id' => 2, 'item_id' => 5, 'content' => 'ノートPCの性能が良さそうです。'],

            // ユーザーID3のコメント
            ['user_id' => 3, 'item_id' => 1, 'content' => '気になっていました！'],
            ['user_id' => 3, 'item_id' => 2, 'content' => 'HDDの信頼性が高そうです！'],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }
    }
}