<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
    // マイページ表示
    public function show(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 'sell');

        $profileImage = $user->profile->profile_image;
        $userName = $user->name;

        $items = collect();
        $purchasedItems = collect();

        // 出品・購入商品ページへ切り替え
        if ($page === 'sell') {
            $items = Item::where('user_id', $user->id)->get();
        } elseif ($page === 'buy') {
            $purchasedItems = Purchase::where('user_id', $user->id)->with('item')->get();
        }

        return view('profile.index', compact('profileImage', 'userName', 'items', 'purchasedItems', 'page'));
    }

    // プロフィール編集ページ表示
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(ProfileRequest $profileRequest)
    {
        $user = auth()->user();

        // ユーザー名の更新
        $user->name = $profileRequest->name;
        $user->save();

        // プロフィールが存在する場合は更新、ない場合は新規作成
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        // 住所情報の更新（ProfileRequest を使用）
        $profile->postal_code = $profileRequest->postal_code;
        $profile->address = $profileRequest->address;
        $profile->building = $profileRequest->building;

        // プロフィール画像の更新（ProfileRequest を使用）
        if ($profileRequest->hasFile('profile_image')) {
            // ファイルを保存する場所を指定（'public'ディスクに保存）
            $imagePath = $profileRequest->file('profile_image')->store('profile_images', 'public');

            // 既存のプロフィール画像があれば削除
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            // 新しい画像パスを保存
            $profile->profile_image = $imagePath;
        }

        $profile->save();

        return redirect('/mypage');
    }
}