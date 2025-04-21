<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class AuthController extends Controller
{
    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // メール認証チェック
    public function verifyCheck()
    {
        $user = Auth::user();

        // 認証が完了している場合はプロフィール編集ページにリダイレクト
        if ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail()) {
            return redirect()->route('profile.edit');
        }

        // 認証が完了していない場合、エラーメッセージを表示
        return redirect()->route('verification.notice')
            ->with('error', 'メール認証が完了していません。');
    }
}