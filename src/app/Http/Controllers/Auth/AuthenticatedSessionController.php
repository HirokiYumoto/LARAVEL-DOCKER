<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // 1. 管理者 (role_id = 3) なら管理者ダッシュボードへ
        if ($user->role_id === 3) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // 2. 店舗代表者 (role_id = 2) なら一般ダッシュボード（または店舗管理画面）へ
        // ※今は一般と同じdashboardですが、分けたい場合はここを変えます
        if ($user->role_id === 2) {
             return redirect()->intended(route('dashboard'));
        }

        // 3. 一般ユーザー (role_id = 1) ならトップページ、または直前のページへ
        return redirect()->intended(route('home'));

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // ★ここを書き換えました
        return redirect()->route('restaurants.index');
    }
}
