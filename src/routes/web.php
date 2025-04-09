<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;

// ログアウト処理
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// トップページ
Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/search', [ItemController::class, 'index'])->name('items.search');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
Route::post('/item/{item_id}/comment', [ItemController::class, 'comment'])->name('item.comment');
Route::post('/item/{item}/like', [ItemController::class, 'like'])->name('item.like');

// 商品購入ページ
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('item.purchase');
    Route::post('/purchase/{item}', [PurchaseController::class, 'submit'])->name('item.purchase.submit');
});

// 住所変更ページ
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'showAddressEdit'])->name('purchase.address.edit');
    Route::patch('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

// 出品ページ
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');
});

// プロフィール関連
Route::middleware(['auth'])->prefix('mypage')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/edit', [ProfileController::class, 'update'])->name('update');
});
