<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ログインしていない、または role_id が 2 (店舗代表者) でない場合は弾く
        // ※必要であれば管理者の role_id 3 も許可に含めてOK (例: ... || Auth::user()->role_id === 3)
        if (!Auth::check() || Auth::user()->role_id !== 2) {
            abort(403, '店舗代表者の権限がありません。');
        }

        return $next($request);
    }
}