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
        $messages = $purchase->messages()->with('user')->orderBy('created_at')->get();

        return view('message.show', compact('purchase', 'messages', 'user'));
    }

    // チャット投稿処理
    public function store(Request $request, Purchase $purchase)
    {
        $user = Auth::user();

        // 自分が購入者か出品者か確認
        if ($user->id !== $purchase->user_id && $user->id !== $purchase->item->user_id) {
            abort(403, 'この取引に投稿できません。');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Message::create([
            'purchase_id' => $purchase->id,
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return redirect()->route('message.show', $purchase->id)
            ->with('success', 'メッセージを送信しました。');
    }

        // 編集
    public function update(Request $request, Purchase $purchase, Message $message)
    {
        $user = Auth::user();

        // 取引に関係ない or 自分のメッセージでない場合は403
        if (
            ($user->id !== $purchase->user_id && $user->id !== $purchase->item->user_id) ||
            $user->id !== $message->user_id
        ) {
            abort(403, '編集できません。');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message->update([
            'content' => $request->content,
        ]);

        return redirect()->route('message.show', $purchase->id)
            ->with('success', 'メッセージを編集しました。');
    }

    // 削除
    public function destroy(Purchase $purchase, Message $message)
    {
        $user = Auth::user();

        if (
            ($user->id !== $purchase->user_id && $user->id !== $purchase->item->user_id) ||
            $user->id !== $message->user_id
        ) {
            abort(403, '削除できません。');
        }

        $message->delete();

        return redirect()->route('message.show', $purchase->id)
            ->with('success', 'メッセージを削除しました。');
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

        return redirect()->route('show', ['page' => 'trading'])
            ->with('success', '取引が完了しました。');
    }
}
