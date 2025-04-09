@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/sell.css')}}">
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
<div class="sell-form">
    {{--  ページタイトル  --}}
    <h2 class="sell-form__heading content__heading">商品の出品</h2>

    {{--  出品フォーム  --}}
        <div class="sell-form__inner">
        <form class="sell-form__form" action="{{ route('item.store') }}" method="post" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- 商品画像--}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="image_path">商品画像</label>
                <div class="image-preview">
                    <img id="image-preview" src="{{ isset($item) && $item->image_path ? asset('storage/' . $item->image_path) : '' }}" alt="{{ $item->name ?? '商品画像プレビュー' }}" class="image-preview__img">
                    <input class="sell-form__input--image" type="file" name="image_path" id="image_path">
                    <label for="image_path" class="sell-form__btn-image">画像を選択する</label>
                </div>
                @error('image_path')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell-form__title">商品の詳細</div>

            {{-- カテゴリ --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="category">カテゴリー</label>
                    <div class="sell-form__category-buttons">
                        @foreach ($categories as $category)
                            <label class="sell-form__category" for="category-{{ $category->id }}">
                                <input type="checkbox" name="category[]" id="category-{{ $category->id }}" value="{{ $category->id }}" {{ in_array($category->id, old('category', [])) ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @error('category')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 商品の状態 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="condition">商品の状態</label>
                <div class="sell-form__condition-dropdown">
                    <div class="condition-trigger" id="custom-dropdown">
                        <span id="selected-option">選択してください</span>
                        <span class="condition-arrow">▼</span>
                    </div>
                    <ul class="condition-list" id="condition-list">
                        @foreach (['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'] as $index => $condition)
                            <li class="condition-item" data-value="{{ $index + 1 }}">
                                <i class="fas fa-check"></i>{{ $condition }}
                            </li>
                        @endforeach
                    </ul>
                    <input type="hidden" name="condition" id="condition-input">
                </div>
                @error('condition')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell-form__title">商品名と説明</div>

            {{-- 商品名 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="name">商品名</label>
                <input class="sell-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- ブランド名 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="brand">ブランド名</label>
                <input class="sell-form__input" type="text" name="brand" id="brand" value="{{ old('brand') }}">
                @error('brand')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 商品説明 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="description">商品の説明</label>
                <textarea class="sell-form__textarea" name="description" id="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 販売価格 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="price">販売価格</label>
                <input type="number" id="price" name="price" class="sell-form__input" value="{{ old('price') }}" placeholder="&yen;">
                @error('price')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 出品ボタン --}}
            <div class="sell-form__btn">
                <input class="sell-form__btn-submit" type="submit" value="出品する">
            </div>
        </form>
    </div>
</div>

<script>
// 画像プレビュー機能
document.querySelector('#image_path').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.querySelector('#image-preview');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// 商品状態のドロップダウン開閉
document.querySelector('#custom-dropdown').addEventListener('click', function() {
    const list = document.querySelector('#condition-list');
    list.classList.toggle('is-visible');
});

// 商品状態の選択
document.querySelectorAll('.condition-item').forEach(function(item) {
    item.addEventListener('click', function() {
        const value = item.getAttribute('data-value');
        document.querySelector('#condition-input').value = value;
        document.querySelector('#selected-option').textContent = item.textContent.trim();

        document.querySelectorAll('.condition-item i').forEach(function(innerItem) {
            innerItem.style.display = 'none';
        });

        item.querySelector('i').style.display = 'inline';
        document.querySelector('#condition-list').classList.remove('is-visible');
    });
});
</script>
@endsection