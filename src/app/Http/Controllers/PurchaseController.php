<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\PurchaseHistory;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

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

    // 購入処理
    public function submit(Request $request, Item $item)
    {
        \Log::debug("購入処理開始");
        \Log::debug("送信されたデータ: " . json_encode($request->all()));

        // 支払い方法の取得とバリデーション
        $validated = $request->validate([
            'payment_method' => 'required|integer|in:1,2', // 数値でバリデーション
        ]);
        \Log::debug("支払い方法: " . $validated['payment_method']);

        try {
            DB::beginTransaction(); // トランザクション開始

            // 購入情報を保存
            $purchase = Purchase::create([
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'payment_method' => $validated['payment_method'],
                'postal_code' => auth()->user()->profile->postal_code ?? '',
                'address' => auth()->user()->profile->address ?? '',
                'building' => auth()->user()->profile->building ?? '',
            ]);
            \Log::debug("購入情報保存完了");

            // 商品の売上ステータスを更新
            $item->update(['sold_status' => true]);
            \Log::debug("商品ステータス更新完了");

            DB::commit(); // コミット（確定）

        } catch (\Exception $e) {
            DB::rollBack(); // ロールバック（失敗時に元に戻す）
            \Log::error("購入処理エラー: " . $e->getMessage());
            return back()->withErrors('購入処理に失敗しました。')->withInput();
        }

        \Log::debug("購入処理完了");

        return redirect('/mypage?tab=buy'); // ホームにリダイレクト
    }
}