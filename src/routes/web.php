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

// トップページ
Route::get('/', [ItemController::class, 'index']);
Route::get('/?tab=mylist', [ItemController::class, 'index'])->middleware('auth');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'show']);
Route::post('/item/{item_id}/comment', [ItemController::class, 'comment']);
Route::post('/item/{item}/like', [ItemController::class, 'like'])->name('item.like');

// ログアウト処理
Route::post('/logout', [AuthController::class, 'logout']);

// プロフィールページ
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'show']);
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileController::class, 'update']);
});


// Route::post('/item/{item_id}/purchase', [ItemController::class, 'purchase'])->name('item.purchase');
// Route::get('/item/purchase/{item}', [PurchaseController::class, 'show'])->name('item.purchase');
// Route::post('/item/purchase/{item}', [PurchaseController::class, 'purchase']);
// Route::middleware('auth')->get('item/purchase/{item}', [ItemController::class, 'purchase'])->name('item.purchase');

// 商品購入ページを表示
Route::get('purchase/{item}', [PurchaseController::class, 'show'])->name('item.purchase');

// 購入処理を行う
Route::post('item/purchase/{item}', [ItemController::class, 'purchase'])->name('item.purchase.submit');