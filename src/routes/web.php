<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RestaurantController; // ← これがあるか確認
use Illuminate\Support\Facades\Route;

// トップページ（ここを修正しました）
Route::get('/', [RestaurantController::class, 'index'])->name('home');

// ▼▼▼ 誰でもアクセスできるエリア ▼▼▼
// （/restaurants でもアクセスできるように残しておいてもOKです）
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
// ...
// ▲▲▲ ここまで ▲▲▲

// ダッシュボード（ログイン後のトップ）
// ▼▼▼ 修正前は1行でしたが、データ取得処理を追加します ▼▼▼
Route::get('/dashboard', function () {
    // ログインユーザーのお気に入りのお店を取得する
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $favorites = $user->favorites()->get();
    $reviews = $user->reviews()->with('restaurant')->get();

return view('dashboard', compact('favorites', 'reviews'));
})->middleware(['auth', 'verified'])->name('dashboard');
// ▲▲▲ ここまで ▲▲▲

// ログインしている人専用のエリア
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // ... プロフィール設定などが書いてある場所 ...

    // ★これを追加（お気に入り機能）
    Route::post('/restaurants/{id}/favorites', [App\Http\Controllers\FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/restaurants/{id}/favorites', [App\Http\Controllers\FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // ★これを追加（レビュー投稿）
    Route::post('/restaurants/{id}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
});
    // ... お店一覧などはそのままでOK ...

require __DIR__.'/auth.php';