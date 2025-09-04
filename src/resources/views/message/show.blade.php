@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/message/show.css') }}">
@endsection

@section('content')
<div class="message">
	{{-- サイドバー --}}
	<aside class="message__sidebar">
		<div class="message__sidebar-title">その他の取引</div>
	</aside>

	<div class="message__main">
		{{-- 取引相手情報 --}}
		<div class="message__trade-partner">
			<div class="trade-partner__info">
				<img src="{{ asset('storage/' . ($partner->profile ? $partner->profile->profile_image : 'default.png')) }}"
					class="trade-partner__image" alt="相手のプロフィール画像">

				<h2 class="trade-partner__name">
					「{{ $partner->name ?? '相手' }}」さんとの取引画面
				</h2>
			</div>

			{{-- 取引完了ボタン(購入者のみ) --}}
			@if ($purchase->status !== 'completed' && $user->id === $purchase->user_id && $user->id !== $purchase->item->user_id)
				<form action="{{ route('message.complete', $purchase) }}" method="POST" class="trade-partner__complete-form">
					@csrf
					<button type="submit" class="trade-partner__complete-btn">取引を完了する</button>
				</form>
			@endif
		</div>

		{{-- 商品情報 --}}
		<div class="message__trade-item">
			<img src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}" class="trade-item__image">
			<div class="trade-item__info">
				<div class="trade-item__name">{{ $purchase->item->name }}</div>
				<p class="trade-item__price">{{ number_format($purchase->item->price) }}円</p>
			</div>
		</div>

		{{-- チャットメッセージ --}}
		@forelse ($messages as $message)
			<div class="message__chat {{ $message->user_id === $user->id ? 'sent' : 'received' }}">

				{{-- ユーザー情報 --}}
				<div class="chat__user {{ $message->user_id === $user->id ? 'chat__user--sent' : 'chat__user--received' }}">
					@if ($message->user_id === $user->id)
						<span class="chat__username">{{ $message->user->name }}</span>
						<img src="{{ asset('storage/' . ($message->user->profile ? $message->user->profile->profile_image : 'default.png')) }}"
							alt="プロフィール画像" class="chat__user-image">
					@else
						<img src="{{ asset('storage/' . ($message->user->profile ? $message->user->profile->profile_image : 'default.png')) }}"
							alt="プロフィール画像" class="chat__user-image">
						<span class="chat__username">{{ $message->user->name }}</span>
					@endif
				</div>

				{{-- メッセージ本文 --}}
				<div class="chat__content-wrapper">
					{{-- 通常表示 --}}
					<div class="chat__content" id="content-{{ $message->id }}">
						{{ $message->content }}
					</div>

					{{-- 画像 --}}
					@if(!empty($message->image_path))
						<div class="chat__image-wrapper">
							<a href="{{ asset('storage/' . $message->image_path) }}" target="_blank" rel="noopener noreferrer">
								<img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像" class="chat__image">
							</a>
						</div>
					@endif

					{{-- 編集フォーム --}}
					<form action="{{ route('message.update', [$purchase->id, $message->id]) }}" method="POST" id="form-{{ $message->id }}" class="chat__edit-form" novalidate>
						@csrf
						@method('PATCH')
						<input type="text" name="content" value="{{ $message->content }}" required class="chat__edit-input">
						<button type="submit" class="chat__edit-btn">保存</button>
						<button type="button" class="chat__edit-btn" onclick="cancelEdit({{ $message->id }})">キャンセル</button>
					</form>
				</div>

				{{-- 編集・削除ボタン（自分のメッセージのみ） --}}
				@if ($message->user_id === $user->id)
					<div class="chat__actions">
						<button type="button" class="chat__actions-btn
						" onclick="editMessage({{ $message->id }})">編集</button>

						<form action="{{ route('message.destroy', [$purchase->id, $message->id]) }}" method="POST">
							@csrf
							@method('DELETE')
							<button type="submit" class="chat__actions-btn" onclick="return confirm('削除しますか？')">削除</button>
						</form>
					</div>
				@endif
			</div>

		@empty
			<p class="chat__empty">取引メッセージはありません。</p>
		@endforelse


		<div class="message__form-wrapper">
			{{-- エラー表示 --}}
			@if ($errors->any())
				<div class="error-message error-message__chat">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			{{-- メッセージ投稿フォーム --}}
			@if ($purchase->status !== 'completed')
				<form action="{{ route('message.store', $purchase) }}" method="POST" class="chat__form" enctype="multipart/form-data">
					@csrf
					<input type="text" name="content" id="chat-input" class="chat__input" placeholder="取引メッセージを記入してください" novalidate>

					{{-- 画像添付 --}}
					<input type="file" name="image" class="chat__image-input" accept="image/*">
					<button type="button" class="chat__image-btn" onclick="document.querySelector('.chat__image-input').click();">
						画像を追加
					</button>

					<button type="submit" class="chat__submit-btn">
						<img src="{{ asset('images/icons/send_icon.png') }}" alt="送信" class="chat__submit-icon">
					</button>
				</form>
			@endif
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

	// 編集フォームを表示
	window.editMessage = function (id) {
		document.getElementById('content-' + id).style.display = 'none';
		document.getElementById('form-' + id).style.display = 'block';
	}

	window.cancelEdit = function (id) {
		document.getElementById('form-' + id).style.display = 'none';
		document.getElementById('content-' + id).style.display = 'block';
	}

	// 入力テキスト保持
	const chatInput = document.getElementById('chat-input');
	const storageKey = 'chat-input-{{ $purchase->id }}';
	const saved = localStorage.getItem(storageKey);
	if (saved) chatInput.value = saved;

	chatInput.addEventListener('input', () => {
		localStorage.setItem(storageKey, chatInput.value);
	});

	document.querySelector('form.chat__form')?.addEventListener('submit', () => {
		localStorage.removeItem(storageKey);
	});

});
</script>
@endsection