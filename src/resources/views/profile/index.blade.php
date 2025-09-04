@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css')}}">
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

@section('nav')
<div class="mypage">
    <div class="mypage__profile">
        <div class="mypage__profile-user">

            <div class="profile-left">
                {{-- プロフィール画像 --}}
                <div class="mypage__profile-image">
                    <img src="{{ asset('storage/' . auth()->user()->profile->profile_image ?? '') }}" alt="プロフィール画像">
                </div>
            </div>

            <div class="profile-right">
                {{-- ユーザー名 --}}
                <h2 class="mypage__profile-name">{{ auth()->user()->name }}</h2>

                {{-- 星評価 --}}
                @if ($averageRating > 0)
                    <div class="mypage__profile-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= $averageRating ? 'active' : '' }}">★</span>
                        @endfor
                    </div>
                @endif
            </div>
        </div>

        {{-- プロフィール編集ボタン --}}
        <div class="profile-edit__btn">
            <a href="{{ route('profile.edit') }}" class="profile-edit__btn-submit">プロフィールを編集</a>
        </div>
    </div>
</div>

{{-- ナビ --}}
<div class="nav">
    <div class="nav__inner">
        <a class="nav__page {{ $page === 'sell' ? 'active' : '' }}" href="{{ route('profile.show', ['page' => 'sell']) }}">出品した商品</a>
        <a class="nav__page {{ $page === 'buy' ? 'active' : '' }}" href="{{ route('profile.show', ['page' => 'buy']) }}">購入した商品</a>
        <a class="nav__page {{ $page === 'trading' ? 'active' : '' }}" href="{{ route('profile.show', ['page' => 'trading']) }}">
            取引中の商品
            @if(isset($tradingPurchases) && $tradingPurchases->count() > 0)
                <span class="nav__badge">{{ $tradingPurchases->count() }}</span>
            @endif
        </a>
    </div>
</div>
@endsection

@section('content')
{{-- 出品した商品 --}}
@if ($page === 'sell')
    <div class="item__list">
        <div class="item__grid-container">
            @foreach ($items as $item)
            <a href="{{ route('item.show', $item->id) }}" class="item__card-link">
                <div class="item__card">
                    <img class="item__card-image" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    <p class="item__card-title">
                        @if ($item->sold_status)
                            <span class="item__card-label">Sold</span>
                        @endif
                        {{ $item->name }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
@endif

{{-- 購入した商品 --}}
@if ($page === 'buy')
    <div class="item__list">
        <div class="item__grid-container">
            @foreach ($purchasedItems as $purchase)
                <a href="{{ route('item.show', $purchase->item->id) }}" class="item__card-link">
                    <div class="item__card">
                        <img class="item__card-image" src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}">
                        <p class="item__card-title">
                            @if ($purchase->item->sold_status)
                                <span class="item__card-label">Sold</span>
                            @endif
                            {{ $purchase->item->name }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

{{-- 取引中の商品 --}}
@if ($page === 'trading')
    <div class="item__list">
        <div class="item__grid-container">
            @foreach ($tradingPurchases as $purchase)
                <a href="{{ route('message.show', $purchase) }}" class="item__card-link">
                    <div class="item__card">
                        {{-- 未読メッセージ数バッジ --}}
                        @if($purchase->unread_count > 0)
                            <span class="notification-badge">{{ $purchase->unread_count }}</span>
                        @endif

                        <img class="item__card-image" src="{{ asset('storage/' . $purchase->item->image_path) }}"
                            alt="{{ $purchase->item->name }}">
                        <p class="item__card-title">
                            @if ($purchase->item->sold_status)
                                <span class="item__card-label">Sold</span>
                            @endif
                            {{ $purchase->item->name }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
@endsection