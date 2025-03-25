@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
{{--  検索ボックス  --}}
<div class="header__search">
    <form class="search-form" action="/search" method="get">
        @csrf
        <input class="search-form__input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{request('keyword')}}">
    </form>
</div>

{{--  ヘッダーリンク  --}}
<div class="header__link">
    {{--  ログインしている場合  --}}
    @if ($isAuth)
        <form action="/logout" method="post">
            @csrf
            <input class="link__logout" type="submit" value="ログアウト">
        </form>
    {{--  ログインしていない場合  --}}
    @else
        <a class="link__login" href="/login">ログイン</a>
    @endif
    <a class="link__mypage" href="/mypage">マイページ</a>
    <a class="link__sell" href="/sell">出品</a>
</div>
@endsection

{{--  ナビ  --}}
@section('nav')
<div class="nav">
    <div class="nav__inner">
        <a class="nav__inner-top {{ Request::is('/') && !request()->query('tab') ? 'active' : '' }}" href="/">おすすめ</a>
        <a class="nav__inner-mylist {{ request()->query('tab') === 'mylist' ? 'active' : '' }}" href="/?tab=mylist">マイリスト</a>
    </div>
</div>
@endsection

@section('content')
{{--  商品リスト  --}}
<div class="item__group">
    <div class="grid-container">
        @foreach ($items as $item)
            <div class="card">
                <a href="/item/{{ $item->id }}">
                    <img class="card-img" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                </a>
                <div class="card-body">
                    <p class="card-title">{{ $item->name }}</p>
                    @if ($item->sold_status)
                        <span class="sold-label">Sold</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection