<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsSet
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // プロフィール設定ページ・更新はスルーする
        if ($request->routeIs('profile.edit', 'profile.update')) {
            return $next($request);
        }

        if ($user && (
            !$user->profile ||
            !$user->profile->profile_image ||
            !$user->profile->postal_code ||
            !$user->profile->address
        )) {
            return redirect()->route('profile.edit')->with('message', 'プロフィールを設定してください');
        }

        return $next($request);
    }
}