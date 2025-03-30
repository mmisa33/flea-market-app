<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ログアウト処理
Route::post('/logout', [AuthController::class, 'logout']);

// トップページ
Route::get('/', [ItemController::class, 'index']);
Route::get('/?tab=mylist', [ItemController::class, 'index'])->middleware('auth');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'show']);
Route::post('/item/{item_id}/comment', [ItemController::class, 'comment']);
Route::post('/item/{item}/like', [ItemController::class, 'like'])->name('item.like');

// 商品購入ページ
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('item.purchase');
    Route::post('/purchase/{item}', [PurchaseController::class, 'submit'])->name('item.purchase.submit');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'showAddressEdit'])->name('purchase.address.edit');
    Route::patch('/purchase/address/{item}', [PurchaseController::class, 'updateAddress']);
});

// プロフィールページ
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileController::class, 'update']);
});

