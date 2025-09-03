<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // 取引チャット画面表示
    public function show(Purchase $purchase)
    {
        $user = Auth::user();

        // 自分が購入者か出品者か確認
        if ($user->id !== $purchase->user_id && $user->id !== $purchase->item->user_id) {
            abort(403, 'この取引にアクセスできません。');
        }

        // 購入者または出品者が見れるチャットメッセージ
        $messages = $purchase->messages()->with('user')->get();

        return view('messages.show', compact('purchase', 'messages', 'user'));
    }

    // 取引完了ボタン処理（購入者のみ）
    public function complete(Purchase $purchase)
    {
        $user = Auth::user();

        if ($user->id !== $purchase->user_id) {
            abort(403, '取引完了できるのは購入者のみです。');
        }

        $purchase->status = 'completed';
        $purchase->save();

        return redirect()->route('profile.show', ['page' => 'trading'])
            ->with('success', '取引が完了しました。');
    }
}
