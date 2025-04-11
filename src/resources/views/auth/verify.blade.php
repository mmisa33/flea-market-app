@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify.css')}}">
@endsection

@section('content')
<div class="email-verification">
    <h2>登録していただいたメールアドレスに認証メールを送付しました。<br>
    メール認証を完了してください。</h2>

    <a href="http://localhost:8025/" target="_blank" class="verification-link">認証はこちらから</a>

    <form class="resend-form" action="{{ route('verification.send') }}" method="POST">
        @csrf
        <input class="resend-form__link" type="submit" value="認証メールを再送する">
    </form>

</div>
@endsection