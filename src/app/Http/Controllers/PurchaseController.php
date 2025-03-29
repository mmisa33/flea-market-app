<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;

class PurchaseController extends Controller
{
    // 商品購入ページを表示
    public function show(Item $item)
    {
        $isAuth = auth()->check();
        if (!$isAuth) {
            return redirect()->route('login');
        }

        $shippingAddress = $item->shipping_address ? json_decode($item->shipping_address, true) : [
            'postal_code' => auth()->user()->profile->postal_code ?? '',
            'address' => auth()->user()->profile->address ?? '',
            'building' => auth()->user()->profile->building ?? '',
        ];

        return view('item.purchase', compact('item', 'shippingAddress'));
    }

    // 住所変更画面を表示
    public function showAddressEdit(Item $item)
    {
        $isAuth = auth()->check();
        if (!$isAuth) {
            return redirect()->route('login');
        }

        $shippingAddress = session('shippingAddress', $item->shipping_address ? json_decode($item->shipping_address, true) : [
            'postal_code' => auth()->user()->profile->postal_code ?? '',
            'address' => auth()->user()->profile->address ?? '',
            'building' => auth()->user()->profile->building ?? '',
        ]);

        return view('purchase.address', compact('item', 'shippingAddress'));
    }

    // 住所更新処理
    public function updateAddress(AddressRequest $request, Item $item)
    {
        $validated = $request->validated();

        $item->update([
            'shipping_address' => json_encode($validated),
        ]);

        session()->flash('shippingAddress', $validated);

        return redirect()->route('item.purchase', ['item' => $item->id]);
    }

    // public function purchase(Request $request, Item $item)
    // {
    //     // 購入処理（例: 購入記録の保存）
    //     // ここにロジックを追加（例: 購入履歴テーブルに保存）

    //     return redirect()->route('index')->with('success', '購入が完了しました！');
    // }
}
