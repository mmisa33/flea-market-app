@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/detail.css')}}">
@endsection

@section('link')
{{--  検索ボックス  --}}
<div class="header__search">
    <form class="search-form" action="/search" method="get">
        @csrf
        <input class="search-form__input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{request('keyword')}}">
    </form>
</div>

{{--  ヘッダーリンク  --}}
<div class="header__link">
    {{--  ログインしている場合  --}}
    @if ($isAuth)
        <form action="/logout" method="post">
            @csrf
            <input class="link__logout" type="submit" value="ログアウト">
        </form>
    {{--  ログインしていない場合  --}}
    @else
        <a class="link__login" href="/login">ログイン</a>
    @endif
    <a class="link__mypage" href="/mypage">マイページ</a>
    <a class="link__sell" href="/sell">出品</a>
</div>
@endsection

@section('content')
<div class="item-detail">
    {{--  商品画像  --}}
    <div class="item-detail__image">
        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
    </div>

    {{--  商品情報  --}}
    <div class="item-detail__info">
        <div class="info-group">
            {{--  商品名  --}}
            <h2 class="info-group__name">{{ $item->name }}</h2>

            {{--  ブランド名  --}}
            <p class="info-group__brand">{{ $item->brand }}</p>

            {{--  価格  --}}
            <div class="info-group__price">
                <p><span class="price-symbol">¥</span><span class="price-number">{{ number_format($item->price) }}</span><span class="price-tax">（税込）</span></p>
            </div>
        </div>

    <!-- いいね数（アイコン） -->
        <p>
            <i class="fas fa-thumbs-up"></i> {{ $likeCount }} いいね
        </p>

        <!-- コメント数（アイコン） -->
        <p>
            <i class="fas fa-comment"></i> {{ $commentCount }} コメント
        </p>




        {{-- 購入ボタン --}}
        <form action="{{ route('item.purchase', ['item_id' => $item->id]) }}" method="POST">
            @csrf
            <input class="purchase-form__btn btn" type="submit" value="購入手続きへ">
        </form>

        {{-- 商品説明 --}}
        <div class="item-detail__description">
            <h3 class="description__title">商品説明</h3>
            <p class="description__text">{{ $item->description }}</p>
        </div>

        {{-- 商品情報 --}}
        <div class="item-detail__additional-info">
            <h3 class="additional-info__title">商品の情報</h3>

            <table class="additional-info__table">
                {{-- カテゴリ --}}
                <tr class="table__row">
                    <th class="table__header">カテゴリー</th>
                    <td class="table__content table__content--category">
                        @foreach($item->categories as $category)
                            <span>{{ $category->name }}</span>
                        @endforeach
                    </td>
                </tr>
                {{-- 商品の状態 --}}
                <tr class="table__row">
                    <th class="table__header">商品の状態</th>
                    <td class="table__content table__content--condition">
                        @switch($item->condition)
                            @case(1)
                                良好
                                @break
                            @case(2)
                                目立った傷や汚れなし
                                @break
                            @case(3)
                                やや傷や汚れあり
                                @break
                            @case(4)
                                状態が悪い
                                @break
                        @endswitch
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
                        <p><strong>{{ $comment->user->name }}</strong>さん:</p>
                        <p>{{ $comment->content }}</p>
                    </div>
                @endforeach
            @endif

            {{-- コメント投稿フォーム --}}
            @auth
                {{-- <form action="{{ route('', $item->id) }}" method="POST"> --}}
                    @csrf
                    <textarea name="content" rows="8" required></textarea><br>
                    <input class="comment-form__btn btn" type="submit" value="コメントする">
                </form>
            @endauth
        </div>
    </div>
</div>
@endsection
