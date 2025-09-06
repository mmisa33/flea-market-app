<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Purchase;
use App\Models\Review;
use App\Mail\PurchaseCompleted;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    // レビュー投稿
    public function store(ReviewRequest $request, Purchase $purchase)
    {
        $user = auth()->user();

        // レビュー作成
        Review::create([
            'from_user_id' => $user->id,
            'to_user_id'   => $request->evaluatee_id,
            'purchase_id'  => $purchase->id,
            'rating'       => $request->rating,
        ]);

        // 購入者か出品者かで完了フラグを更新
        if ($user->id === $purchase->user_id) {
            $purchase->buyer_completed = true;
        } elseif ($user->id === $purchase->item->user_id) {
            $purchase->seller_completed = true;
        }

        // 双方が完了していたら商品を sold にする
        if ($purchase->buyer_completed && $purchase->seller_completed) {
            $purchase->item->sold_status = true;
            $purchase->item->save();
        }

        $purchase->save();

        // 購入者が完了したタイミングで出品者へ通知メール送信
        if ($user->id === $purchase->user_id) {
            Mail::to($purchase->item->user->email)
                ->send(new PurchaseCompleted($purchase));
        }

        return redirect()->route('home');
    }
}