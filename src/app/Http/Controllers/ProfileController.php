<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
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

    // プロフィール更新
    public function update(ProfileRequest $profileRequest)
    {
        $user = auth()->user();

        $user->name = $profileRequest->name;
        $user->save();

        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $profile->postal_code = $profileRequest->postal_code;
        $profile->address = $profileRequest->address;
        $profile->building = $profileRequest->building;

        if ($profileRequest->hasFile('profile_image')) {
            $imagePath = $profileRequest->file('profile_image')->store('images/profiles', 'public');

            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $profile->profile_image = $imagePath;
        }

        $profile->save();

        // 初回設定の場合はトップページへリダイレクト
        if ($profile->wasRecentlyCreated) {
            return redirect()->route('home'); // 初回設定のためトップページにリダイレクト
        }

        return redirect()->route('profile.show'); // 2回目以降はプロフィールページにリダイレクト
    }
}