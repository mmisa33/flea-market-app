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

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/mylist', [ItemController::class, 'index'])->name('items.mylist')->middleware('auth');

// ログアウトルート
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');