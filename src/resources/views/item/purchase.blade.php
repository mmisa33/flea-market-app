@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/purchase.css')}}">
@endsection

@section('link')
{{-- 検索ボックス --}}
<div class="header__search">
    <form class="search-form" action="/search" method="get">
        @csrf
        <input class="search-form__input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{request('keyword')}}">
    </form>
</div>

{{-- ヘッダーリンク --}}
<div class="header__link">
    <form action="/logout" method="post">
        @csrf
        <input class="link__logout" type="submit" value="ログアウト">
    </form>
    <a class="link__mypage" href="/mypage">マイページ</a>
    <a class="link__sell" href="/sell">出品</a>
</div>
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-info__group">
        <div class="purchase-item__info">
            {{-- 商品画像 --}}
            <div class="item-image">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
            </div>

            <div class="item__details">
                {{-- 商品名 --}}
                <h2 class="item-name">{{ $item->name }}</h2>

                {{-- 価格 --}}
                <div class="item-price">
                    <p><span class="price-symbol">¥&nbsp;</span>{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>

        {{-- 支払い方法選択 --}}
        <div class="payment-method">
            <div class="payment-method__header">
                <h3>支払い方法</h3>
            </div>
            <div class="payment-method__select-wrapper">
                <select class="payment-method__select" name="payment_method" id="payment_method">
                    <option value="">選択してください</option>
                    <option value="コンビニ払い">コンビニ支払い</option>
                    <option value="カード支払い">カード支払い</option>
                </select>
            </div>
        </div>

        <!-- 住所 -->
        <div class="delivery-address">
            <div class="delivery-address__header">
                <h3>配送先住所</h3>
                <a href="/purchase/address/{{ $item->id }}">住所変更</a>
            </div>
            <div class="delivery-address__detail">
                <p>{{ auth()->user()->profile->postal_code }}</p>
                <p>{{ auth()->user()->profile->address }}{{ auth()->user()->profile->building }}</p>
            </div>
        </div>
    </div>

    <div class="purchase-confirm__group">
        <table class="confirm-table">
            <tr class="confirm-table__row">
                <th class="confirm-table__header">商品代金</th>
                <td class="confirm-table__item">
                    <div class="confirm__item-price">
                        <p><span class="confirm__price-symbol">¥&nbsp;</span>{{ number_format($item->price) }}</p>
                    </div>
                </td>
            </tr>
            <tr class="confirm-table__row">
                <th class="confirm-table__header">支払い方法</th>
                <td class="confirm-table__item" id="selected-payment-method"></td>
            </tr>
        </table>

        <!-- 購入ボタン -->
        <form  class="purchase-btn" action="{{ route('item.purchase.submit', ['item' => $item->id]) }}" method="POST">
            @csrf
            <input class="purchase-btn__submit btn" type="submit" value="購入する">
        </form>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const paymentSelect = document.getElementById("payment_method");
    const paymentDisplay = document.getElementById("selected-payment-method");

    paymentSelect.addEventListener("change", function () {
        if (paymentSelect.value) {
            paymentDisplay.textContent = paymentSelect.value;
        } else {
            paymentDisplay.textContent = "";
        }
    });
});
</script>
@endsection


