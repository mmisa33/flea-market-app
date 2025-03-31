@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/sell.css')}}">
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
<div class="sell-form">
    {{--  ページタイトル  --}}
    <h2 class="sell-form__heading content__heading">商品の出品</h2>

    {{--  出品フォーム  --}}
        <div class="sell-form__inner">
        <form class="sell-form__form" action="{{ route('item.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            {{-- 商品画像--}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="image_path">商品画像</label>
                <div class="image-preview">
                    <img id="image-preview" src="{{ isset($item) && $item->image_path ? asset('storage/' . $item->image_path) : '' }}" alt="{{ $item->name ?? '商品画像プレビュー' }}" style="display:none;">
                    <input class="sell-form__input--image" type="file" name="image_path" id="image_path">
                    <label for="image_path" class="sell-image__btn">画像を選択する</label>
                </div>
                @error('image_path')
                    <p class="sell-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell-form__title">商品の詳細</div>

            {{-- カテゴリ --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="category">カテゴリー</label>
                    <div class="category-buttons">
                        @foreach ($categories as $category)
                            <label class="category-button">
                                <input type="checkbox" name="category[]" value="{{ $category->id }}"
                                {{ in_array($category->id, old('category', [])) ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @error('category')
                    <p class="sell-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 商品の状態 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="condition">商品の状態</label>
                <div class="condition-dropdown">
                    <div class="condition-trigger" id="custom-dropdown">
                        <span id="selected-option">選択してください</span>
                        <span class="condition-arrow">▼</span>
                    </div>
                    <ul class="condition-list" id="condition-list">
                        <li class="condition-item" data-value="1">
                            <i class="fas fa-check"></i>良好
                        </li>
                        <li class="condition-item" data-value="2">
                            <i class="fas fa-check"></i>目立った傷や汚れなし
                        </li>
                        <li class="condition-item" data-value="3">
                            <i class="fas fa-check"></i>やや傷や汚れあり
                        </li>
                        <li class="condition-item" data-value="4">
                            <i class="fas fa-check"></i>状態が悪い
                        </li>
                    </ul>
                    <input type="hidden" name="condition" id="condition-input">
                </div>
                @error('condition')
                    <p class="sell-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell-form__title">商品名と説明</div>

            {{-- 商品名 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="name">商品名</label>
                <input class="sell-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
                @error('name')
                    <p class="sell-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- ブランド名 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="brand">ブランド名</label>
                <input class="sell-form__input" type="text" name="brand" id="brand" value="{{ old('brand') }}">
                @error('brand')
                    <p class="sell-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 商品説明 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="description">商品の説明</label>
                <textarea class="sell-form__textarea" name="description" id="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <p class="sell-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 販売価格 ※textでいいか後で確認 --}}
            <div class="sell-form__group">
                <label class="sell-form__label" for="price">販売価格</label>
                <input type="number" id="price" name="price" class="sell-form__input" value="{{ old('price') }}" placeholder="&yen;">
                @error('price')
                    <p class="sell-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 出品ボタン --}}
            <div class="sell-form__btn-inner">
                <input class="sell-form__btn" type="submit" value="出品する">
            </div>
        </form>
    </div>
</div>

<script>
// document.getElementById('price').addEventListener('input', function(event) {
//     const input = event.target;

//     // 初期値が "¥" であることを保証
//     if (input.value.indexOf('¥') !== 0) {
//         input.value = '¥' + input.value.replace(/[^0-9]/g, ''); // 数字以外を除外
//     }
// });

// document.querySelector('form').addEventListener('submit', function(event) {
//     let priceField = document.getElementById('price');
//     // ¥を取り除いて送信
//     priceField.value = priceField.value.replace('¥', '').replace(/[^0-9]/g, '');  // 数字以外を除去
// });

document.getElementById('image_path').addEventListener('change', function(event) {
    const file = event.target.files[0];  // 選択されたファイル
    const preview = document.getElementById('image-preview');  // 画像プレビューの要素

    if (file) {
        const reader = new FileReader();  // FileReaderを使って画像を読み込む

        reader.onload = function(e) {
            preview.src = e.target.result;  // 読み込んだ画像をプレビューに設定
            preview.style.display = 'block';  // プレビューを表示
        }

        reader.readAsDataURL(file);  // ファイルをデータURLとして読み込む
    }
});




document.addEventListener('DOMContentLoaded', function() {
    // ドロップダウンをクリックしたときの処理
    document.getElementById('custom-dropdown').addEventListener('click', function() {
        const list = document.getElementById('condition-list');
        list.style.display = list.style.display === 'block' ? 'none' : 'block';
    });

    // 各選択肢をクリックしたときの処理
    document.querySelectorAll('.condition-item').forEach(function(item) {
        item.addEventListener('click', function() {
            // 選択された値を隠しフィールドにセット
            const value = item.getAttribute('data-value');
            document.getElementById('condition-input').value = value;

            // テキストの更新
            const selectedText = item.textContent.trim();
            document.getElementById('selected-option').textContent = selectedText;

            // アイコンの表示/非表示の処理
            document.querySelectorAll('.condition-item').forEach(function(innerItem) {
                // すべての選択肢からチェックマークを非表示
                innerItem.querySelector('i').style.display = 'none';
            });

            // クリックされた選択肢にチェックマークを表示
            item.querySelector('i').style.display = 'inline';

            // ドロップダウンを閉じる
            document.getElementById('condition-list').style.display = 'none';
        });
    });

    // ドロップダウンを開く時に表示を整える処理
    const list = document.getElementById('condition-list');
    list.style.display = 'none'; // 初期状態で非表示
});
</script>
@endsection