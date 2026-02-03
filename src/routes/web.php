<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController; // 必要なら追加
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 一般公開ルート (ログイン不要)
|--------------------------------------------------------------------------
*/

// トップページ・一覧
Route::get('/', [RestaurantController::class, 'index'])->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

// 詳細ページ
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');

/*
|--------------------------------------------------------------------------
| 認証済みユーザー用ルート (ログイン必須)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // プロフィール管理
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // レビュー投稿・削除
    Route::post('/restaurants/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy'); // 追加

    // お気に入り
    Route::post('/restaurants/{id}/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/restaurants/{id}/favorites', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

/*
|--------------------------------------------------------------------------
| 店舗オーナー専用ルート (ログイン + owner権限)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'owner'])->group(function () {
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
    Route::delete('/restaurants/{id}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy'); // 追加
});

/*
|--------------------------------------------------------------------------
| 管理者専用ルート (ログイン + admin権限)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AdminController; // ★冒頭のuseエリアに追加するか、ここで記述

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    
    // ダッシュボード表示
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // 削除機能
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::delete('/restaurants/{id}', [AdminController::class, 'destroyRestaurant'])->name('admin.restaurants.destroy');
    Route::delete('/reviews/{id}', [AdminController::class, 'destroyReview'])->name('admin.reviews.destroy');

});

require __DIR__.'/auth.php';



