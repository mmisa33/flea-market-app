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
        $authId = $user->id; // ループ内で毎回呼ばないように
        $page = $request->query('page', 'sell');

        // プロフィール情報
        $profileImage = optional($user->profile)->profile_image;
        $userName = $user->name;

        // ユーザーのレビュー平均（四捨五入）
        $averageRating = round($user->reviewsReceived()->avg('score') ?? 0);

        // 出品商品
        $items = Item::where('user_id', $authId)->get();

        // 購入商品
        $purchasedItems = Purchase::where('user_id', $authId)
            ->with('item')
            ->get();

        // 取引中の商品（購入者・出品者）
        $tradingPurchases = Purchase::where('status', 'trading')
            ->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)
                    ->orWhereHas('item', function ($q2) use ($authId) {
                        $q2->where('user_id', $authId);
                    });
            })
            ->with(['item.messages' => function($q) {
                $q->latest()
                    ->select(
                        'messages.id',
                        'messages.purchase_id',
                        'messages.user_id',
                        'messages.content',
                        'messages.is_read',
                        'messages.created_at'
                    );
            }])
            ->get()
            ->map(function ($purchase) use ($authId) {
                $messages = $purchase->item->messages;
                $latestMessage = $messages->first();

                // 最新メッセージ日時（ソート用）
                $purchase->latest_message_at = optional($latestMessage)->created_at;

                // 未読メッセージ数
                $purchase->unread_count = $messages
                    ->where('user_id', '!=', $authId)
                    ->where('is_read', false)
                    ->count();

                return $purchase;
            })
            ->sortByDesc('latest_message_at')
            ->values();

        // ページ切り替え
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