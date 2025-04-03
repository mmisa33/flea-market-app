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
        <form class="login-form__form" action="/login" method="post">
            @csrf
            {{-- メールアドレス入力 --}}
            <div class="login-form__group">
                <label class="login-form__label" for="email">メールアドレス</label>
                <input class="login-form__input" type="email" name="email" id="email" value="{{ old('email') }}" >
                {{-- エラーメッセージ --}}
                <p class="login-form__error-message">
                    @error('email')
                        @if ($message !== __('auth.failed'))
                        {{ $message }}
                        @endif
                    @enderror
                </p>
            </div>

            {{-- パスワード入力 --}}
            <div class="login-form__group">
                <label class="login-form__label" for="password">パスワード</label>
                <input class="login-form__input" type="password" name="password" id="password">
                {{-- エラーメッセージ --}}
                <p class="login-form__error-message">
                @error('password')
                {{ $message }}
                @enderror
                </p>
            </div>

            <div class="login-form__actions">
                {{-- ログインボタン --}}
                <input class="login-form__btn" type="submit" value="ログイン">

                {{--  会員登録ページへ移行  --}}
                <a class="login-form__link" href="/register">会員登録はこちら</a>

                {{-- エラーメッセージ --}}
                <p class="login-form__error-message">
                    @if (session('errors'))
                        @foreach (session('errors')->get('email') as $error)
                            @if ($error === trans('auth.failed'))
                                {{ $error }}
                            @endif
                        @endforeach
                    @endif
                </p>
            </div>
        </form>
    </div>
</div>
@endsection