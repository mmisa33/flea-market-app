<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();
        $tab = $request->query('tab', 'sell');

        // ユーザーのプロフィール情報を取得
        $profileImage = $user->profile->profile_image;
        $userName = $user->name;

        // 出品した商品一覧（デフォルト）
        $items = collect();
        $purchasedItems = collect();

        if ($tab === 'sell') {
            $items = Item::where('user_id', $user->id)->get();
        } elseif ($tab === 'buy') {
            $purchasedItems = Purchase::where('user_id', $user->id)->with('item')->get();
        }

        return view('profile.index', compact('profileImage', 'userName', 'items', 'purchasedItems', 'tab'));
    }

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