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
    <h1>商品購入</h1>

    <div class="product-info">
        <!-- 商品画像 -->
        <div class="product-image">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
        </div>

        <!-- 商品名 -->
        <div class="product-name">
            <h2>{{ $item->name }}</h2>
        </div>

        <!-- 価格 -->
        <div class="product-price">
            <p>価格: ¥{{ number_format($item->price) }}</p>
        </div>

        <!-- 住所 -->
        <div class="delivery-address">
            <p><strong>配送先住所:</strong> {{ auth()->user()->address ?? '住所未設定' }}</p>
            <!-- 住所変更リンク -->
            <a href="/profile/edit">住所変更</a>
        </div>
    </div>

    <hr>

    <!-- 支払い方法選択 -->
    <div class="payment-method">
        <label for="payment_method">支払い方法</label>
        <select name="payment_method" id="payment_method">
            <option value="convenience_store">コンビニ支払い</option>
            <option value="credit_card">カード支払い</option>
        </select>
    </div>

    <hr>

    <!-- 購入ボタン -->
    <form action="{{ route('item.purchase.submit', ['item' => $item->id]) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">購入する</button>
    </form>
</div>

<!-- 小計反映用スクリプト -->
<script>
    const paymentMethodSelect = document.getElementById('payment_method');
    paymentMethodSelect.addEventListener('change', function() {
        const selectedMethod = paymentMethodSelect.value;
        console.log('選択された支払い方法:', selectedMethod);
    });
</script>
@endsection
