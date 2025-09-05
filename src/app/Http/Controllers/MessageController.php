<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // 参加者チェック
    private function isParticipant($user, Purchase $purchase): bool
    {
        return $user->id === $purchase->user_id || $user->id === $purchase->item->user_id;
    }

    // 取引メッセージページ表示
    public function show(Purchase $purchase)
    {
        $user = Auth::user();

        if (!$this->isParticipant($user, $purchase)) {
            abort(403, 'この取引にアクセスできません');
        }

        // サイドバー用の他の取引（未読→新規メッセージ順）
        $otherPurchases = Purchase::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                ->orWhereHas('item', fn($q2) => $q2->where('user_id', $user->id));
            })
            ->where(function ($q) {
                $q->where('buyer_completed', false)
                ->orWhere('seller_completed', false);
            })
            ->where('id', '!=', $purchase->id) // 今表示してる取引は除外
            ->with(['item.messages' => function ($q) {
                $q->latest()
                ->select('messages.id', 'messages.purchase_id', 'messages.user_id', 'messages.content', 'messages.is_read', 'messages.created_at');
            }])
            ->get()
            ->map(function ($p) use ($user) {
                $messages = $p->item->messages;

                // 最新メッセージ日時
                $latestMessage = $messages->first();
                $p->latest_message_at = optional($latestMessage)->created_at;

                // 未読メッセージ数
                $p->unread_count = $messages
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();

                return $p;
            })
            ->sortByDesc(fn($p) => [$p->unread_count > 0 ? 1 : 0, $p->latest_message_at])
            ->values();

        // メッセージ取得（昇順）
        $messages = $purchase->messages()
            ->with('user.profile')
            ->orderBy('created_at')
            ->get();

        // 取引相手の判定
        $partner = ($user->id === $purchase->user_id)
            ? $purchase->item->user   // 自分が購入者 → 相手は出品者
            : $purchase->user;        // 自分が出品者 → 相手は購入者

        // 未読メッセージ判定
        $purchase->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // モーダル判定
        $showReviewModal = false;
        $evaluateeId = null;

        if ($purchase->status === 'completed' && $purchase->item && $purchase->item->user) {
            // 購入者がまだ評価していない場合
            if ($user->id === $purchase->user_id && !$purchase->buyer_completed) {
                $showReviewModal = true;
                $evaluateeId = $purchase->item->user->id; // 出品者
            }

            // 出品者がまだ評価していない場合
            if ($user->id === $purchase->item->user_id && !$purchase->seller_completed) {
                $showReviewModal = true;
                $evaluateeId = $purchase->user_id; // 購入者
            }
        }

        return view('message.show', compact(
            'purchase',
            'messages',
            'user',
            'otherPurchases',
            'partner',
            'showReviewModal',
            'evaluateeId'
        ));
    }

    // メッセージ投稿
    public function store(MessageRequest $request, Purchase $purchase)
    {
        $user = Auth::user();

        if (!$this->isParticipant($user, $purchase)) {
            abort(403, '投稿権限がありません');
        }

        $data = $request->validated();

        $message = new Message([
            'purchase_id' => $purchase->id,
            'user_id'     => $user->id,
            'content'     => $data['content'],
        ]);

        if ($request->hasFile('image')) {
            $message->image_path = $request->file('image')
                ->store('images/messages', 'public');
        }

        $message->save();

        return redirect()->route('message.show', $purchase);
    }

    // メッセージ編集
    public function update(MessageRequest $request, Purchase $purchase, Message $message)
    {
        $user = Auth::user();

        if (!$this->isParticipant($user, $purchase) || $user->id !== $message->user_id) {
            abort(403, '編集権限がありません');
        }

        $message->update($request->validated());

        return redirect()->route('message.show', $purchase);
    }

    // メッセージ削除
    public function destroy(Purchase $purchase, Message $message)
    {
        $user = Auth::user();

        if (!$this->isParticipant($user, $purchase) || $user->id !== $message->user_id) {
            abort(403, '削除権限がありません');
        }

        $message->delete();

        return redirect()->route('message.show', $purchase);
    }
}