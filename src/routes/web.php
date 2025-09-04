<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// ログアウト処理
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// トップページ表示・検索機能
Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/search', [ItemController::class, 'index'])->name('items.search');

// メール認証チェック
Route::get('/verify/check', [AuthController::class, 'verifyCheck'])->name('verify.check');

// 商品詳細ページ表示
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

// ログインユーザー専用ページ
Route::middleware(['auth', 'verified', 'profile.set'])->group(function () {
    // コメント・いいね機能
    Route::post('/item/{item_id}/comment', [ItemController::class, 'comment'])->name('item.comment');
    Route::post('/item/{item}/like', [ItemController::class, 'like'])->name('item.like');

    // 商品購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('item.purchase');
    Route::post('/purchase/{item}', [PurchaseController::class, 'submit'])->name('item.purchase.submit');

    // 配送先住所の変更
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'showAddressEdit'])->name('purchase.address.edit');
    Route::patch('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 商品出品
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

    // マイページ（プロフィール）
    Route::prefix('mypage')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('update');

        // 取引チャット画面表示
        Route::get('/message/{purchase}', [MessageController::class, 'show'])
            ->name('message.show');

        // 取引チャット投稿
        Route::post('/message/{purchase}', [MessageController::class, 'store'])
            ->name('message.store');

        // 取引完了ボタン
        Route::post('/message/{purchase}/complete', [MessageController::class, 'complete'])
            ->name('message.complete');
    });
});