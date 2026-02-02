<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController; // ★この行があるか確認
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// トップページ（一覧）
Route::get('/', [RestaurantController::class, 'index'])->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

// ★★★ 重要：create は {id} よりも「前」に書く必要があります！ ★★★
// ★ 'auth' だけでなく 'owner' も追加してガードを強化
Route::middleware(['auth', 'owner'])->group(function () {
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
});

// 詳細ページ（これは create の「後」に書く）
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
// ▲▲▲▲▲▲

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// レビュー投稿・お気に入り（既存の機能）
Route::middleware('auth')->group(function () {
    Route::post('/restaurants/{id}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/restaurants/{id}/favorites', [App\Http\Controllers\FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/restaurants/{id}/favorites', [App\Http\Controllers\FavoriteController::class, 'destroy'])->name('favorites.destroy');
});
Route::middleware(['auth', 'owner'])->group(function () {
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
    
    // ★追加：削除用ルート
    Route::delete('/restaurants/{id}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ... その他のルート ...

    // ★追加：レビュー削除用ルート
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});
require __DIR__.'/auth.php';