<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // ログインしていない、または権限が管理者(3)でない場合は弾く
        if (!Auth::check() || Auth::user()->role_id !== 3) {
            // 403 Forbidden（権限なし）を返す、またはトップへリダイレクト
            abort(403, '管理者権限がありません。');
        }

        return $next($request);
    }
}
