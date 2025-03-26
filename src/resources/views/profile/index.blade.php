@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css')}}">
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

@section('nav')
<div class="mypage-container">
    <div class="mypage__profile">
        <div class="mypage__profile-item">
            <!-- プロフィール画像 -->
            <div class="profile__item-image">
                <img src="{{ asset('storage/' . auth()->user()->profile->profile_image ?? '') }}" alt="Profile Image" class="profile__item-image--preview">
            </div>

            <!-- ユーザー名 -->
            <h2 class="profile__item-name">{{ auth()->user()->name }}</h2>
        </div>
        <!-- プロフィール編集ボタン -->
        <div class="profile-edit__btn">
            <a href="/mypage/profile" class="profile-edit__btn-submit btn">プロフィールを編集</a>
        </div>
    </div>
</div>

<div class="nav">
    <div class="nav__inner">
        <a class="nav__inner" href="">出品した商品</a>
        <a class="nav__inner" href="">購入した商品</a>
    </div>
</div>
@endsection

@section('content')

@endsection