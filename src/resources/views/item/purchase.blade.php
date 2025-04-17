@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/purchase.css')}}">
@endsection

@section('link')
{{-- 検索ボックス --}}
<div class="header__search">
    <form class="search-form" action="{{ route('items.search') }}" method="get">
        <input class="search-form__input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
    </form>
</div>

{{-- ヘッダーリンク --}}
<div class="header__links">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <input class="header__link header__link--logout" type="submit" value="ログアウト">
    </form>
    <a class="header__link header__link--mypage" href="{{ route('profile.show') }}">マイページ</a>
    <a class="header__link--sell" href="{{ route('item.create') }}">出品</a>
</div>
@endsection

@section('content')
<div class="item-purchase">
    <div class="item-purchase__content">
        <div class="item-purchase__info">
            {{-- 商品画像 --}}
            <div class="item-purchase__info-image">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
            </div>

            <div class="item-purchase__info-details">
                {{-- 商品名 --}}
                <h2 class="details__name">{{ $item->name }}</h2>

                {{-- 価格 --}}
                <div class="details__price">
                    <p><span class="price-symbol">¥&nbsp;</span>{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>

        {{-- 支払い方法選択 --}}
        <div class="item-purchase__payment-method">
            <div class="payment-method__header">
                <h3>支払い方法</h3>
            </div>
            <div class="payment-method__dropdown">
                <div class="payment-method__trigger" id="custom-dropdown">
                    <span id="selected-option">選択してください</span>
                    <span class="payment-method__arrow">▼</span>
                </div>
                <ul class="payment-method__list">
                    <li class="payment-method__item"  data-value="konbini">
                        <i class="fas fa-check"></i>コンビニ払い
                    </li>
                    <li class="payment-method__item" data-value="card">
                        <i class="fas fa-check"></i>カード支払い
                    </li>
                </ul>
                <p class="error-message">
                    @error('payment_method')
                        {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        {{-- 配送先住所 --}}
        <div class="item-purchase__delivery-address">
            <div class="delivery-address__header">
                <h3>配送先住所</h3>
                <a href="{{ route('purchase.address.edit', ['item' => $item->id]) }}">住所変更</a>
            </div>
            <div class="delivery-address__detail">
                <p>{{ $shippingAddress['postal_code'] ?? auth()->user()->profile->postal_code }}</p>
                <p>
                    {{ $shippingAddress['address'] ?? auth()->user()->profile->address }}
                    @if(!empty($shippingAddress['building']))
                        {{ $shippingAddress['building'] }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- 代金と支払い方法確認 --}}
    <div class="item-purchase__confirm">
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
        @if ($item->sold_status)
            <button class="purchase-form__btn btn--disabled" disabled>購入済み</button>
        @else
            <form action="{{ route('item.purchase.submit', ['item' => $item->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method-hidden">
                <input class="purchase-form__btn btn" type="submit" value="購入する">
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ドロップダウンボタンとリスト要素の取得
    const dropdown = document.getElementById('custom-dropdown');
    const list = document.querySelector('.payment-method__list');
    const selectedOption = document.getElementById('selected-option');
    const hiddenInput = document.getElementById('payment-method-hidden');
    const options = document.querySelectorAll('.payment-method__item');
    const paymentDisplay = document.getElementById('selected-payment-method');

    // ドロップダウンボタンをクリック時に支払い方法のリストを表示/非表示
    dropdown.addEventListener('click', function () {
        list.style.display = (list.style.display === 'block') ? 'none' : 'block';
    });

    // 支払い方法の選択肢クリック時の処理
    options.forEach(function (item) {
        item.addEventListener('click', function () {
            const value = item.getAttribute('data-value');
            hiddenInput.value = value;

            // 選択された支払い方法のテキストを表示
            const selectedText = item.textContent.trim();
            selectedOption.textContent = selectedText;
            paymentDisplay.textContent = selectedText;

            // すべての選択肢のアイコンを非表示にし、選択したもののみアイコンを表示
            options.forEach(opt => opt.querySelector('i').style.display = 'none');
            item.querySelector('i').style.display = 'inline';

            list.style.display = 'none';
        });
    });

    // ドロップダウンリストの外側をクリックした場合にリストを閉じる
    document.addEventListener('click', function (event) {
        if (!dropdown.contains(event.target) && !list.contains(event.target)) {
            list.style.display = 'none';
        }
    });
});
</script>
@endsection