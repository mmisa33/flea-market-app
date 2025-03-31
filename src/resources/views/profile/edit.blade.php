@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css')}}">
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
<div class="profile-form">
    {{-- ページタイトル --}}
    <h2 class="profile-form__heading content__heading">プロフィール設定</h2>

    {{-- ユーザー登録フォーム --}}
    <div class="profile-form__inner">
        <form class="profile-form__form" action="/mypage/profile" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- プロフィール画像設定 --}}
            <div class="profile-form__group">
                <div class="profile__image">
                    <div class="profile__image-preview">
                        <img id="profile-image-preview"
        src="{{ auth()->user()->profile && auth()->user()->profile->profile_image ? asset('storage/' . auth()->user()->profile->profile_image) : '' }}"
        >
                    </div>

                    <div class="profile__image-btn">
                        <label for="profile_image" class="profile__image--label">画像を選択する</label>
                        <input type="file" name="profile_image" id="profile_image" class="profile__image--input" style="display: none;" onchange="previewImage(event)">
                        {{-- エラーメッセージ --}}
                        <p class="profile-form__error-message">
                            @error('profile_image')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                </div>
            </div>

            {{-- 名前入力 --}}
            <div class="profile-form__group">
                <label class="profile-form__label" for="name">ユーザー名</label>
                <input class="profile-form__input" type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}">
                {{-- エラーメッセージ --}}
                <p class="profile-form__error-message">
                @error('name')
                {{ $message }}
                @enderror
                </p>
            </div>

            {{-- 郵便番号入力 --}}
            <div class="profile-form__group">
                <label class="profile-form__label" for="postal_code">郵便番号</label>
                <input class="profile-form__input" type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->user()->profile->postal_code ?? '') }}">
                {{-- エラーメッセージ --}}
                <p class="profile-form__error-message">
                @error('postal_code')
                {{ $message }}
                @enderror
                </p>
            </div>

            {{-- 住所入力 --}}
            <div class="profile-form__group">
                <label class="profile-form__label" for="address">住所</label>
                <input class="profile-form__input" type="text" name="address" id="address" value="{{ old('address', auth()->user()->profile->address ?? '') }}">
                {{-- エラーメッセージ --}}
                <p class="profile-form__error-message">
                @error('address')
                {{ $message }}
                @enderror
                </p>
            </div>

            {{-- メールアドレスを hidden で送信（表示はしない --}}
            <div class="profile-form__group">
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
            </div>

            {{-- 建物名入力 --}}
            <div class="profile-form__group">
                <label class="profile-form__label" for="building">建物名</label>
                <input class="profile-form__input" type="text" name="building" id="building" value="{{ old('building', auth()->user()->profile->building ?? '') }}">
            </div>

            {{-- 登録ボタン --}}
            <div class="confirm-form__btn-inner">
                <input class="profile-form__btn" type="submit" value="更新する">
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const input = event.target;
    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function (e) {
        const imagePreview = document.getElementById('profile-image-preview');
        const imgElement = imagePreview;
        imagePreview.src = e.target.result;

        imagePreview.style.backgroundColor = 'transparent';
        imgElement.style.display = 'block';
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}
</script>
@endsection