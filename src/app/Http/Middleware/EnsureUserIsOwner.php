<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ログインしていない、または店舗オーナー(role_id=2)でない場合
        if (! $request->user() || ! $request->user()->isStoreOwner()) {
            // トップページへ強制送還
            return redirect('/');
        }

        return $next($request);
    }
}