<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    // 商品購入ページを表示
    public function show(Item $item)
    {
        $shippingAddress = session("shippingAddress_{$item->id}", [
            'postal_code' => auth()->user()->profile->postal_code ?? '',
            'address' => auth()->user()->profile->address ?? '',
            'building' => auth()->user()->profile->building ?? '',
        ]);

        return view('item.purchase', compact('item', 'shippingAddress'));
    }

    // 住所変更画面を表示
    public function showAddressEdit(Item $item)
    {
        $isAuth = auth()->check();
        if (!$isAuth) {
            return redirect()->route('login');
        }

        // セッションに保存された住所があればそれを使う
        $shippingAddress = session("shippingAddress_{$item->id}", [
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
        $paymentMethod = 1;

        // 既存の購入レコードを取得
        $purchase = Purchase::where('item_id', $item->id)
            ->where('user_id', auth()->id())
            ->first();

        // 既存のレコードがあれば更新
        if ($purchase) {
            $purchase->update([
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building' => $validated['building'] ?? null,
                'payment_method' => $paymentMethod
            ]);
        }

        // セッションに新しい住所情報を保存
        session(["shippingAddress_{$item->id}" => $validated]);

        // 住所変更後の画面へリダイレクト
        return redirect()->route('item.purchase', ['item' => $item->id]);
    }

    // 購入処理
    public function submit(PurchaseRequest $request, Item $item)
    {
        $validated = $request->validated();

        // セッションから送付先住所を取得
        $shippingAddress = session("shippingAddress_{$item->id}", [
            'postal_code' => auth()->user()->profile->postal_code ?? '',
            'address' => auth()->user()->profile->address ?? '',
            'building' => auth()->user()->profile->building ?? '',
        ]);

        // 購入情報を保存
        $purchase = Purchase::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'payment_method' => $validated['payment_method'],
            'postal_code' => $shippingAddress['postal_code'],
            'address' => $shippingAddress['address'],
            'building' => $shippingAddress['building'],
        ]);

        // 商品の売上ステータスを更新
        $item->update(['sold_status' => true]);

        return redirect()->route('home');
    }
}