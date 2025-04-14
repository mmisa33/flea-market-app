@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/detail.css')}}">
@endsection

@section('link')
{{--  検索ボックス  --}}
<div class="header__search">
    <form class="search-form" action="{{ route('items.search') }}" method="get">
        <input class="search-form__input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
    </form>
</div>

{{--  ヘッダーリンク  --}}
<div class="header__links">
    @auth
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <input class="header__link header__link--logout" type="submit" value="ログアウト">
        </form>
    @else
        <a class="header__link header__link--login" href="{{ route('login') }}">ログイン</a>
    @endauth

    <a class="header__link header__link--mypage" href="{{ route('profile.show') }}">マイページ</a>
    <a class="header__link--sell" href="{{ route('item.create') }}">出品</a>
</div>
@endsection

@section('content')
<div class="item-detail">
    {{-- 商品画像 --}}
    <div class="item-detail__image">
        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
    </div>

    {{-- 商品情報 --}}
    <div class="item-detail__info">
        <div class="item-detail__info-group">
            {{-- 商品名 --}}
            <h2 class="info-group__name">
                @if ($item->sold_status)
                <span class="sold-label">Sold</span>
                @endif
                {{ $item->name }}
            </h2>

            {{-- ブランド名 --}}
            <p class="info-group__brand">{{ $item->brand }}</p>

            {{-- 価格 --}}
            <div class="info-group__price">
                <p>
                    <span class="price-symbol">¥</span>
                    <span class="price-number">{{ number_format($item->price) }}</span>
                    <span class="price-tax">（税込）</span>
                </p>
            </div>
        </div>

        <div class="item-detail__icon">
            {{-- いいね数（アイコン） --}}
            <div class="icon__like">
                @auth
                    <form action="{{ route('item.like', $item) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="icon__like-button">
                            <img src="{{ asset($liked ? 'images/icons/yellow_star_icon.png' : 'images/icons/star_icon.png') }}" alt="Like Icon" class="icon__like-image">
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="icon__like-button">
                        <img src="{{ asset('images/icons/star_icon.png') }}" alt="Like Icon" class="icon__like-image">
                    </a>
                @endauth
                {{ $likeCount }}
            </div>

            {{-- コメント数（アイコン） --}}
            <div class="icon__comment">
                <img src="{{ asset('images/icons/comment_icon.png') }}" alt="Comment Icon"> {{ $commentCount }}
            </div>
        </div>

        {{-- 購入手続きボタン --}}
        <a href="{{ route('item.purchase', ['item' => $item->id]) }}" class="to-purchase-form__btn btn {{ $item->sold_status || $isOwnItem ? 'btn--disabled' : '' }}">
            {{ $item->sold_status ? '売り切れました' : ($isOwnItem ? 'あなたの出品です' : '購入手続きへ') }}
        </a>

        {{-- 商品説明 --}}
        <div class="item-detail__description">
            <h3 class="description__title">商品説明</h3>
            <p class="description__text">{{ $item->description }}</p>
        </div>

        {{-- 商品情報 --}}
        <div class="item-detail__properties">
            <h3 class="properties__title">商品の情報</h3>

            <table class="properties-table">
                {{-- カテゴリ --}}
                <tr class="properties-table__row">
                    <th class="properties-table__header">カテゴリー</th>
                    <td class="properties-table__content properties-table__content--category">
                        @foreach($item->categories as $category)
                            <span>{{ $category->name }}</span>
                        @endforeach
                    </td>
                </tr>
                {{-- 商品の状態 --}}
                <tr class="properties-table__row">
                    <th class="properties-table__header">商品の状態</th>
                    <td class="properties-table__content properties-table__content--condition">
                        {{ $conditionLabel }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- コメントセクション --}}
        <div class="item-detail__comments">
            <h3 class="comments__title">コメント({{ $item->comments->count() }})</h3>

            @if($item->comments->count() > 0)
                @foreach($item->comments as $comment)
                <div class="comment">
                    <div class="comment__user">
                        <div class="comment__user-image">
                            <img src="{{ asset('storage/' . $comment->user->profile->profile_image) }}" alt="プロフィール画像">
                        </div>
                        <span class="comment__user-name">{{ $comment->user->name }}</span>
                    </div>
                    <div class="comment__content">{{ $comment->content }}</div>
                </div>
                @endforeach
            @endif

            {{-- コメント投稿フォーム --}}
            <form class="comment-form" action="{{ route('item.comment', $item->id) }}" method="POST" {{ $item->sold_status ? 'disabled' : '' }} novalidate>
                @csrf
                <div class="comment-form__title">商品へのコメント</div>
                <textarea class="comment-form__textarea-input" name="content" rows="10">{{ old('content') }}</textarea><br>
                @error('content')
                    <p class="error-message">{{ $message }}</p>
                @enderror
                <input class="comment-form__btn btn {{ $item->sold_status ? 'btn--disabled' : '' }}" type="submit" value="コメントする" {{ $item->sold_status ? 'disabled' : '' }}>
            </form>
        </div>
    </div>
</div>
@endsection
