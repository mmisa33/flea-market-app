@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="register-form">
    {{--  ページタイトル  --}}
    <h2 class="register-form__heading content__heading">会員登録</h2>

    {{--  ユーザー登録フォーム  --}}
    <div class="register-form__inner">
        <form class="register-form__form" action="/register" method="post">
            @csrf
            <div class="register-form__group">
                <label class="register-form__label" for="name">ユーザー名</label>
                <input class="register-form__input" type="text" name="name" id="name">
                {{--  エラーメッセージ  --}}
                <p class="register-form__error-message">
                @error('name')
                {{ $message }}
                @enderror
                </p>
            </div>

            {{--  メールアドレス入力  --}}
            <div class="register-form__group">
                <label class="register-form__label" for="email">メールアドレス</label>
                <input class="register-form__input" type="mail" name="email" id="email">
                {{--  エラーメッセージ  --}}
                <p class="register-form__error-message">
                @error('email')
                {{ $message }}
                @enderror
                </p>
            </div>

            {{--  パスワード入力  --}}
            <div class="register-form__group">
                <label class="register-form__label" for="password">パスワード</label>
                <input class="register-form__input" type="password" name="password" id="password">
                {{--  エラーメッセージ  --}}
                <p class="register-form__error-message">
                @error('password')
                {{ $message }}
                @enderror
                </p>
            </div>

            {{--  確認用パスワード入力  --}}
            <div class="register-form__group">
                <label class="register-form__label" for="password_confirmation">確認用パスワード</label>
                <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation">
                {{--  エラーメッセージ  --}}
                <p class="register-form__error-message">
                @error('password_confirmation')
                {{ $message }}
                @enderror
                </p>
            </div>

            <div class="confirm-form__btn-inner">
                {{--  登録ボタン  --}}
                <input class="register-form__btn" type="submit" value="登録する">

                {{--  ログインページへ移行  --}}
                <a class="register-form__link" href="/login">ログインはこちら</a>
            </div>
        </form>
    </div>
</div>
@endsection