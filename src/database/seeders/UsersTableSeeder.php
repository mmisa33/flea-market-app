<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ユーザーを3名作成
        $users = [
            [
                'name' => '田中 太郎',
                'email' => 'taro@example.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'profile_image' => 'images/profiles/user01.jpg',
                    'postal_code' => '100-0001',
                    'address' => '東京都千代田区千代田1-1',
                    'building' => '千代田ビル',
                ],
            ],
            [
                'name' => '鈴木 次郎',
                'email' => 'jiro@example.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'profile_image' => 'images/profiles/user02.jpg',
                    'postal_code' => '150-0001',
                    'address' => '東京都渋谷区渋谷1-2-3',
                    'building' => '渋谷ビル',
                ],
            ],
            [
                'name' => '佐藤 花子',
                'email' => 'hanako@example.com',
                'password' => Hash::make('password123'),
                'profile' => [
                    'profile_image' => 'images/profiles/user03.jpg',
                    'postal_code' => '160-0004',
                    'address' => '東京都新宿区新宿1-1-1',
                    'building' => '新宿ビル',
                ],
            ],
        ];

        foreach ($users as $userData) {
            // ユーザー作成
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'email_verified_at' => Carbon::now(), // メール認証済みとして設定
            ]);

            // プロフィールを作成
            $user->profile()->create($userData['profile']);
        }
    }
}