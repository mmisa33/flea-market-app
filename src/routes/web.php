<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;

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
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
Route::post('/item/{item_id}/comment', [ItemController::class, 'comment'])->name('item.comment.store');

// ログアウト処理
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/item/{item_id}/purchase', [ItemController::class, 'purchase'])->name('item.purchase');