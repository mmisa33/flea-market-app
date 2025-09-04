@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/message/show.css') }}">
@endsection

@section('content')
<div class="message">
{{-- サイドバー --}}
<aside class="message__sidebar">
    <div>その他の取引</div>
</aside>

<div class="message__container">
    {{-- 取引相手情報 --}}
    <div class="trade-partner__info">
        <div class="trade-partner__left">
            <img src="{{ asset('storage/' . ($purchase->item->user->profile ? $purchase->item->user->profile->profile_image : 'default.png')) }}" class="trade-partner__image" alt="相手のプロフィール画像">

            <h2 class="trade-partner__name">
                「{{ $purchase->item->user->name ?? '相手' }}」さんとの取引画面
            </h2>
        </div>

        {{-- 取引完了ボタン --}}
        @if ($purchase->status !== 'completed' && $user->id === $purchase->user_id)
            <form action="{{ route('profile.message.complete', $purchase) }}" method="POST" class="trade-complete-form">
                @csrf
                <button type="submit" class="trade-complete-form__btn">取引を完了する</button>
            </form>
        @endif
    </div>

    {{-- 商品情報 --}}
    <div class="trade-item__info">
        <img src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}" class="trade-item__image">
        <div class="trade-item-text">
            <div class="trade-item__name">{{ $purchase->item->name }}</div>
            <p class="trade-item__price">{{ number_format($purchase->item->price) }}円</p>
        </div>
    </div>

    {{-- チャットメッセージ --}}
    @forelse ($messages as $message)
        <div class="chat-message {{ $message->user_id === $user->id ? 'sent' : 'received' }}">

            {{-- ユーザー情報 --}}
            <div class="chat-message__user {{ $message->user_id === $user->id ? 'sent-header' : 'received-header' }}">
                @if ($message->user_id === $user->id)
                    <span class="chat-message__user-name">{{ $message->user->name }}</span>
                    <img src="{{ asset('storage/' . ($message->user->profile ? $message->user->profile->profile_image : 'default.png')) }}"
                        alt="プロフィール画像" class="chat-message__user-image">
                @else
                    <img src="{{ asset('storage/' . ($message->user->profile ? $message->user->profile->profile_image : 'default.png')) }}"
                        alt="プロフィール画像" class="chat-message__user-image">
                    <span class="chat-message__user-name">{{ $message->user->name }}</span>
                @endif
            </div>

            {{-- メッセージ本文 --}}
            <div class="chat-message__content-wrapper">
                {{-- 通常表示 --}}
                <div class="chat-message__content" id="content-{{ $message->id }}">
                    {{ $message->content }}
                </div>

                {{-- 編集フォーム（非表示） --}}
                <form action="{{ route('profile.message.update', [$purchase->id, $message->id]) }}" method="POST" id="form-{{ $message->id }}" class="chat-message__edit-form" style="display: none;">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="content" value="{{ $message->content }}" required class="chat-message__edit-input">
                    <button type="submit" class="chat-message__edit-btn">保存</button>
                    <button type="button" class="chat-message__edit-btn" onclick="cancelEdit({{ $message->id }})">キャンセル</button>
                </form>
            </div>

            {{-- 編集・削除ボタン（自分のメッセージのみ） --}}
            @if ($message->user_id === $user->id)
                <div class="chat-message__actions">
                    <button type="button" class="chat-message__actions-btn" onclick="editMessage({{ $message->id }})">編集</button>

                    <form action="{{ route('profile.message.destroy', [$purchase->id, $message->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="chat-message__actions-btn" onclick="return confirm('削除しますか？')">削除</button>
                    </form>
                </div>
            @endif
        </div>

        @empty
            <p class="chat-message__empty">取引メッセージはありません。</p>
        @endforelse


        {{-- メッセージ投稿フォーム --}}
        @if ($purchase->status !== 'completed')
            <form action="{{ route('profile.message.store', $purchase) }}" method="POST" class="trade-form">
                @csrf
                <input type="text" name="content" class="trade-form__content" placeholder="取引メッセージを記入してください" novalidate>
                <button type="button" class="trade-form__image-btn">画像を追加</button>
                <button type="submit" class="trade-form__submit-btn">
                    <img src="{{ asset('images/icons/send_icon.png') }}" alt="送信" class="send-icon">
                </button>
            </form>
        @endif
    </div>
</div>

{{-- 編集用JS --}}
<script>
function editMessage(id) {
    document.getElementById('content-' + id).style.display = 'none';
    document.getElementById('form-' + id).style.display = 'block';
}

function cancelEdit(id) {
    document.getElementById('form-' + id).style.display = 'none';
    document.getElementById('content-' + id).style.display = 'block';
}
</script>
@endsection