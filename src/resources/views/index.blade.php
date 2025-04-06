@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('link')
{{--  検索ボックス  --}}
<div class="header__search">
    <form class="search-form" action="{{ route('items.search') }}" method="get">
        <input class="search-form__input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
    </form>
</div>

{{--  ヘッダーリンク  --}}
<div class="header__links">
    @auth
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <input class="header__link header__link--logout" type="submit" value="ログアウト">
        </form>
    @else
        <a class="header__link header__link--login" href="{{ route('login') }}">ログイン</a>
    @endauth

    <a class="header__link header__link--mypage" href="{{ route('profile.show') }}">マイページ</a>
    <a class="header__link--sell" href="{{ route('item.create') }}">出品</a>
</div>
@endsection

{{--  ナビ  --}}
@section('nav')
<nav class="nav">
    <div class="nav__inner">
        <a class="nav__page {{ $activePage === 'recommended' ? 'active' : '' }}" href="{{ url('/' . (request('keyword') ? '?keyword=' . request('keyword') : '')) }}">おすすめ</a>
        <a class="nav__page {{ $activePage === 'mylist' ? 'active' : '' }}" href="{{ url('/?page=mylist' . (request('keyword') ? '&keyword=' . request('keyword') : '')) }}">マイリスト</a>
    </div>
</nav>
@endsection

@section('content')
{{--  商品リスト  --}}
<div class="item__list">
    <div class="item__grid-container">
        @foreach ($items as $item)
            <div class="item__card">
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                    <img class="item__card-image" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                </a>
                <p class="item__card-title">
                    @if ($item->sold_status)
                        <span class="item__card-label">Sold</span>
                    @endif
                    {{ $item->name }}
                </p>
            </div>
        @endforeach
    </div>
</div>
@endsection