<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MessageRequest;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // 取引メッセージ画面表示
    public function show(Purchase $purchase)
    {
        $user = Auth::user();

        // 自分が購入者か出品者か確認
        if ($user->id !== $purchase->user_id && $user->id !== $purchase->item->user_id) {
            abort(403, 'この取引にアクセスできません。');
        }

        // メッセージ取得（作成日時順）
        $messages = $purchase->messages()->with('user')->orderBy('created_at')->get();

        // 取引相手を判定
        $partner = ($user->id === $purchase->user_id)
            ? $purchase->item->user // 自分が購入者なら出品者を相手に
            : $purchase->user;      // 自分が出品者なら購入者を相手に

        // 未読メッセージを自分用に既読に更新
        $purchase->messages()
            ->where('user_id', '!=', $user->id)   // 自分以外の投稿
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('message.show', compact('purchase', 'messages', 'user', 'partner'));
    }

    // メッセージ投稿処理
    public function store(MessageRequest $request, Purchase $purchase)
    {
        $user = auth()->user();

        $message = new Message();
        $message->purchase_id = $purchase->id;
        $message->user_id = $user->id;
        $message->content = $request->input('content');

        // 画像があれば保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/messages', 'public');
            $message->image_path = $path;
        }

        $message->save();

        return redirect()->back();
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
