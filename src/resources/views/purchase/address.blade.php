@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css')}}">
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
@php
    $hasOld = count(session()->getOldInput()) > 0;
@endphp

{{-- 送付先変更フォーム --}}
<div class="address-form">
    {{-- ページタイトル --}}
    <h2 class="address-form__heading content__heading">住所の変更</h2>

    {{-- 送付先変更フォーム --}}
    <div class="address-form__inner">
        <form class="address-form__form" action="{{ route('purchase.address.update', $item->id) }}" method="post">
            @csrf
            @method('PATCH')

            {{-- 郵便番号入力 --}}
            <div class="address-form__group">
                <label class="address-form__label" for="postal_code">郵便番号</label>
                <input class="address-form__input" type="text" name="postal_code" id="postal_code" value="{{ $hasOld ? old('postal_code') : ($shippingAddress['postal_code'] ?? '') }}">
                <p class="error-message">
                    @error('postal_code')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            {{-- 住所入力 --}}
            <div class="address-form__group">
                <label class="address-form__label" for="address">住所</label>
                <input class="address-form__input" type="text" name="address" id="address" value="{{ old('address', $shippingAddress['address'] ?? '') }}">
                <p class="error-message">
                    @error('address')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            {{-- 建物名入力 --}}
            <div class="address-form__group">
                <label class="address-form__label" for="building">建物名</label>
                <input class="address-form__input" type="text" name="building" id="building" value="{{ old('building', $shippingAddress['building'] ?? '') }}">
            </div>

            {{-- 登録ボタン --}}
            <div class="address-form__btn">
                <input class="address-form__btn-submit" type="submit" value="更新する">
            </div>
        </form>
    </div>
</div>
@endsection