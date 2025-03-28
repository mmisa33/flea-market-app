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

        return view('item.purchase', compact('item'));
    }

    // 住所変更画面表示
    public function showAddressEdit(Item $item)
    {
        $isAuth = auth()->check();
        if (!$isAuth) {
            return redirect()->route('login');
        }

        // ユーザーの住所を取得
        $userAddress = auth()->user()->profile->address ?? '';
        return view('purchase.address', compact('item', 'userAddress'));
    }

    // 住所更新処理
    public function updateAddress(Request $request, Item $item)
    {
        // ユーザーの住所を更新
        $user = auth()->user();
        $user->profile->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // 住所変更後に購入ページにリダイレクト
        return redirect()->route('purchase.page', ['item' => $item->id]);
    }

    // public function purchase(Request $request, Item $item)
    // {
    //     // 購入処理（例: 購入記録の保存）
    //     // ここにロジックを追加（例: 購入履歴テーブルに保存）

    //     return redirect()->route('index')->with('success', '購入が完了しました！');
    // }
}
