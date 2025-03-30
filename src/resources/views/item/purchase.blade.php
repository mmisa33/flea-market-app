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
            <div class="payment-method__dropdown">
                <div class="payment-method__trigger" id="custom-dropdown">
                    <span id="selected-option">選択してください</span>
                    <span class="payment-method__arrow">▼</span>
                </div>
                <ul class="payment-method__list">
                    <li class="payment-method__item" data-value="コンビニ払い">
                        <span class="payment-method__checkmark">✔</span>コンビニ払い
                    </li>
                    <li class="payment-method__item" data-value="カード支払い">
                        <span class="payment-method__checkmark">✔</span>カード支払い
                    </li>
                </ul>
                <input type="hidden" name="payment_method" id="payment-method__input">
            </div>
        </div>

        {{-- 配送先住所 --}}
        <div class="delivery-address">
            <div class="delivery-address__header">
                <h3>配送先住所</h3>
                <a href="/purchase/address/{{ $item->id }}">住所変更</a>
            </div>
            <div class="delivery-address__detail">
                <p> {{ $shippingAddress['postal_code'] ?? '' }}</p>
                <p>{{ $shippingAddress['address'] ?? '' }}{{ $shippingAddress['building'] ?? '' }}</p>
            </div>
        </div>
    </div>

    {{-- 代金と支払い方法確認 --}}
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

        {{-- 購入ボタン --}}
        <form  class="purchase-btn" action="" method="POST">
            @csrf
            <input class="purchase-btn__submit btn" type="submit" value="購入する">
        </form>
    </div>
</div>

{{-- 支払い方法選択後に確認欄に即時反映させる処理 --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('custom-dropdown');
    const optionsList = document.querySelector('.payment-method__list');
    const selectedOption = document.getElementById('selected-option');
    const hiddenInput = document.getElementById('payment-method__input');
    const options = document.querySelectorAll('.payment-method__item');
    const paymentDisplay = document.getElementById("selected-payment-method");

    // デフォルトの支払い方法を設定（hiddenInputと確認欄のみ設定）
    const defaultPayment = "コンビニ払い";
    hiddenInput.value = defaultPayment;
    paymentDisplay.textContent = defaultPayment;

    dropdown.addEventListener('click', function () {
        dropdown.classList.toggle('open');
    });

    // 選択肢をクリックしたときの処理
    options.forEach(option => {
        option.addEventListener('click', function () {
            selectedOption.innerHTML = this.innerHTML.replace('✔', '');
            hiddenInput.value = this.dataset.value;
            dropdown.classList.remove('open');
            paymentDisplay.textContent = hiddenInput.value;
        });
    });

    document.addEventListener('click', function (event) {
        if (!dropdown.contains(event.target) && !optionsList.contains(event.target)) {
            dropdown.classList.remove('open');
        }
    });
});
</script>
@endsection


