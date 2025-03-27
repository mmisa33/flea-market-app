<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $isAuth = auth()->check();
        if (!$isAuth) {
            return redirect()->route('login');
        }

        // 商品購入ページのビューを表示
        return view('item.purchase', compact('item'));
    }

    // public function purchase(Request $request, Item $item)
    // {
    //     // 購入処理（例: 購入記録の保存）
    //     // ここにロジックを追加（例: 購入履歴テーブルに保存）

    //     return redirect()->route('index')->with('success', '購入が完了しました！');
    // }
}
