@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
<div class="login-form">
    {{--  ページタイトル  --}}
    <h2 class="login-form__heading content__heading">ログイン</h2>

    {{-- ログインフォーム --}}
    <div class="login-form__inner">
        <form class="login-form__form" action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            {{-- メールアドレス入力 --}}
            <div class="login-form__group">
                <label class="login-form__label" for="email">メールアドレス</label>
                <input class="login-form__input" type="email" name="email" id="email" value="{{ old('email') }}">
                <p class="error-message">
                    @error('email')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            {{-- パスワード入力 --}}
            <div class="login-form__group">
                <label class="login-form__label" for="password">パスワード</label>
                <input class="login-form__input" type="password" name="password" id="password">
                <p class="error-message">
                    @error('password')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <div class="login-form__actions">
                {{-- ログインボタン --}}
                <input class="login-form__btn" type="submit" value="ログイン">

                {{--  会員登録ページへ移行  --}}
                <a class="login-form__link" href="{{ route('register') }}">会員登録はこちら</a>
            </div>
        </form>
    </div>
</div>
@endsection