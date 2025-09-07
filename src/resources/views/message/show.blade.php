@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/message/show.css') }}">
<link rel="stylesheet" href="{{ asset('css/message/modal.css') }}">
@endsection

@section('content')
<div class="message">
	{{-- サイドバー --}}
	<aside class="message__sidebar">
		<div class="message__sidebar-title">その他の取引</div>
		<ul class="message__sidebar-list">
			@foreach ($otherPurchases as $p)
				<li class="message__sidebar-item">
					<a href="{{ route('message.show', $p->id) }}">
						{{ $p->item->name }}
					</a>
				</li>
			@endforeach
		</ul>
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
			@if ($user->id === $purchase->user_id)
				@if ($purchase->buyer_completed)
					{{-- 出品者の評価待ち --}}
					<button type="button" class="trade-partner__complete-btn btn--disabled" disabled>
						出品者の評価待ち
					</button>
				@else
					{{-- まだ完了していない場合 --}}
					<button type="button" id="open-review-modal" class="trade-partner__complete-btn"
						onclick="openModal('{{ $purchase->item->user_id }}')">
						取引を完了する
					</button>
				@endif
			@endif
		</div>

		{{-- 商品情報 --}}
		<div class="message__trade-item">
			<img src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}"
				class="trade-item__image">
			<div class="trade-item__info">
				<div class="trade-item__name">{{ $purchase->item->name }}</div>
				<p class="trade-item__price">{{ number_format($purchase->item->price) }}円</p>
			</div>
		</div>

		{{-- チャットメッセージ --}}
		@forelse ($messages as $message)
			<div class="message__chat {{ $message->user_id === $user->id ? 'sent' : 'received' }}">
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

				<div class="chat__content-wrapper">
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

					{{-- メッセージ編集詳細 --}}
					<form action="{{ route('message.update', [$purchase->id, $message->id]) }}" method="POST"
						id="form-{{ $message->id }}" class="chat__edit-form" novalidate>
						@csrf
						@method('PATCH')
						<input type="text" name="content" value="{{ $message->content }}" required class="chat__edit-input">
						<button type="submit" class="chat__edit-btn">保存</button>
						<button type="button" class="chat__edit-btn" onclick="cancelEdit({{ $message->id }})">キャンセル</button>
					</form>
				</div>

				{{-- メッセージ編集・削除リンク --}}
				@if ($message->user_id === $user->id)
					<div class="chat__actions">
						<button type="button" class="chat__actions-btn" onclick="editMessage({{ $message->id }})">編集</button>
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

		{{-- メッセージ投稿フォーム --}}
		@if ($purchase->status !== 'completed')
			<div class="message__form-wrapper fixed-form">

				{{-- エラーメッセージ --}}
				@if ($errors->has('content') || $errors->has('image'))
					<div class="error-message error-message__chat">
						<ul>
							@foreach ($errors->get('content') as $error)
								<li>{{ $error }}</li>
							@endforeach
							@foreach ($errors->get('image') as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<form action="{{ route('message.store', $purchase) }}" method="POST" class="chat__form"
					enctype="multipart/form-data">
					@csrf
					<input type="text" name="content" id="chat-input" class="chat__input" placeholder="取引メッセージを記入してください" novalidate>
					<input type="file" name="image" class="chat__image-input" accept="image/*">
					<button type="button" class="chat__image-btn" onclick="document.querySelector('.chat__image-input').click();">
						画像を追加
					</button>
					<button type="submit" class="chat__submit-btn">
						<img src="{{ asset('images/icons/send_icon.png') }}" alt="送信" class="chat__submit-icon">
					</button>
				</form>
			</div>
		@endif
	</div>
</div>

{{-- 評価モーダル --}}
@include('message.modal')

<script>
document.addEventListener('DOMContentLoaded', () => {

	// メッセージ編集
	window.editMessage = id => {
		document.getElementById('content-' + id).style.display = 'none';
		document.getElementById('form-' + id).style.display = 'block';
	}

	window.cancelEdit = id => {
		document.getElementById('form-' + id).style.display = 'none';
		document.getElementById('content-' + id).style.display = 'block';
	}

	// チャット入力保持
	const chatInput = document.getElementById('chat-input');
	const storageKey = 'chat-input-{{ $purchase->id }}';
	const saved = localStorage.getItem(storageKey);
	if (saved) chatInput.value = saved;

	chatInput?.addEventListener('input', () => {
		localStorage.setItem(storageKey, chatInput.value);
	});

	document.querySelector('form.chat__form')?.addEventListener('submit', () => {
		localStorage.removeItem(storageKey);
	});

	// モーダル制御
	const modal = document.getElementById('reviewModal');
	const stars = document.querySelectorAll('#star-rating .star');
	const ratingInput = document.getElementById('rating');
	const evaluateeInput = document.getElementById('evaluatee_id');

	window.openModal = function (evaluateeId = null) {
		if (!modal) return;
		if (evaluateeId) evaluateeInput.value = evaluateeId;
		modal.style.display = 'block';
		modal.classList.add('active');
	}

	window.closeModal = function () {
		if (!modal) return;
		modal.style.display = 'none';
		modal.classList.remove('active');
	}

	// モーダル外クリックで閉じる
	modal?.addEventListener('click', e => {
		if (!e.target.closest('.modal__content')) closeModal();
	});

	// 星クリック
	stars.forEach(star => {
		star.addEventListener('click', function () {
			const value = parseInt(this.dataset.value);
			ratingInput.value = value;
			stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= value));
		});
	});

	// バリデーションエラー表示
	@if($errors->has('rating') || $errors->has('evaluatee_id'))
		if (modal) modal.style.display = 'block';
	@endif

	// 自動表示条件（出品者のみ）
	@if($user->id === $purchase->item->user_id && $purchase->buyer_completed && !$purchase->seller_completed)
		openModal("{{ $purchase->user_id }}"); // 評価対象は購入者
	@endif
});
</script>
@endsection