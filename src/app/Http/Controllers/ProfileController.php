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

        $profileImage = $user->profile->profile_image ?? null;
        $userName = $user->name;

        // ユーザーのレビュー平均を取得し、四捨五入
        $averageRating = round($user->reviewsReceived()->avg('score') ?? 0);

        // 出品商品
        $items = Item::where('user_id', $user->id)->get();

        // 購入商品
        $purchasedItems = Purchase::where('user_id', $user->id)
            ->with('item')
            ->get();

        // 取引中の商品（購入者または出品者として関わるもの）
        $tradingPurchases = Purchase::where('status', 'trading')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                ->orWhereHas('item', fn($q2) => $q2->where('user_id', $user->id));
            })
            ->with(['item.messages' => function($q) {
                $q->orderByDesc('created_at'); // メッセージ最新順
            }])
            ->get()
            ->map(function ($purchase) {
                // 未読メッセージ数を追加
                $purchase->unread_count = $purchase->messages
                    ->where('user_id', '!=', auth()->id())
                    ->where('is_read', false)
                    ->count();

                // 最新メッセージの日時を取得（ソート用）
                $purchase->latest_message_at = $purchase->messages->first()->created_at ?? null;

                return $purchase;
            })
            ->sortByDesc('latest_message_at')
            ->values();

        if ($page !== 'sell') $items = collect();
        if ($page !== 'buy') $purchasedItems = collect();

        return view('profile.index', compact(
            'profileImage',
            'userName',
            'items',
            'purchasedItems',
            'tradingPurchases',
            'page',
            'averageRating'
        ));
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