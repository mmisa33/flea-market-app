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
            ['user_id' => 1, 'item_id' => 2, 'content' => 'とても良さそうですね！'],
            ['user_id' => 1, 'item_id' => 3, 'content' => 'デザインが好みです。'],

            // ユーザーID2のコメント
            ['user_id' => 2, 'item_id' => 1, 'content' => 'かっこいい腕時計ですね！'],
            ['user_id' => 2, 'item_id' => 9, 'content' => '使いやすそうです。'],

            // ユーザーID3のコメント
            ['user_id' => 3, 'item_id' => 1, 'content' => '気になっていました！'],
            ['user_id' => 3, 'item_id' => 5, 'content' => '性能が良さそうです！'],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }
    }
}