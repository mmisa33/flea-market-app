<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    // 商品購入ページ表示
    public function show(Item $item)
    {
        $shippingAddress = $this->getShippingAddress($item);

        return view('item.purchase', compact('item', 'shippingAddress'));
    }

    // 住所変更画面表示
    public function showAddressEdit(Item $item)
    {
        $shippingAddress = session("shippingAddress_{$item->id}") ?? [
            'postal_code' => '',
            'address' => '',
            'building' => '',
        ];

        return view('purchase.address', compact('item', 'shippingAddress'));
    }

    // 住所更新処理
    public function updateAddress(AddressRequest $request, Item $item)
    {
        $validated = $request->validated();
        session(["shippingAddress_{$item->id}" => $validated]);

        return redirect()->route('item.purchase', ['item' => $item->id]);
    }

    // 商品購入処理
    public function submit(PurchaseRequest $request, Item $item)
    {
        $validated = $request->validated();
        $shippingAddress = $this->getShippingAddress($item);

        // 住所情報をAddressesテーブルに保存または更新
        $address = Address::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'postal_code' => $shippingAddress['postal_code'],
                'address' => $shippingAddress['address']
            ],
            [
                'building' => $shippingAddress['building'] ?? null
            ]
        );

        // 購入情報を作成
        Purchase::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => $validated['payment_method']
        ]);

        $item->update(['sold_status' => true]);

        // 支払い方法によってStripeセッションを作成
        $paymentMethod = $validated['payment_method'];

        if ($paymentMethod == 'konbini') { // コンビニ支払い
            // コンビニ支払い用のセッション作成
            $session = Session::create([
                'payment_method_types' => ['konbini'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'unit_amount' => $item->price,
                        'product_data' => [
                            'name' => $item->name,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
            ]);
        } else if ($paymentMethod == 'card') { // カード支払い
            // カード支払い用のセッション作成
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'unit_amount' => $item->price,
                        'product_data' => [
                            'name' => $item->name,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
            ]);
        }

        // 一時的に購入情報保存（購入確定は成功後）
        session(["purchase_info_{$item->id}" => [
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => $validated['payment_method']
        ]]);

        // Stripeの決済画面にリダイレクト
        return redirect($session->url);
    }

    // セッションプロフィールから送付先を取得
    private function getShippingAddress(Item $item)
    {
        return session("shippingAddress_{$item->id}", [
            'postal_code' => auth()->user()->profile->postal_code ?? '',
            'address' => auth()->user()->profile->address ?? '',
            'building' => auth()->user()->profile->building ?? '',
        ]);
    }
}