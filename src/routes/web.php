<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AdminController; // 追加
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController; // ★ファイルの上のほうに追加
use App\Http\Controllers\OwnerController; // ★ファイルの上のほうに追加

/*
|--------------------------------------------------------------------------
| 一般公開ルート (一覧など)
|--------------------------------------------------------------------------
*/
Route::get('/', [RestaurantController::class, 'index'])->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

/*
|--------------------------------------------------------------------------
| 認証済みユーザー用ルート
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/restaurants/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/restaurants/{id}/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/restaurants/{id}/favorites', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

/*
|--------------------------------------------------------------------------
| 店舗オーナー専用ルート (create は {id} より前に書く！)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'owner'])->group(function () {
    // ★ ここにある create が先に判定されるのでOKになります
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
    Route::delete('/restaurants/{id}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');
});

/*
|--------------------------------------------------------------------------
| 管理者専用ルート
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::delete('/restaurants/{id}', [AdminController::class, 'destroyRestaurant'])->name('admin.restaurants.destroy');
    Route::delete('/reviews/{id}', [AdminController::class, 'destroyReview'])->name('admin.reviews.destroy');
});


/*

*/
// ここを一番下に持ってくることで、上の create などに当てはまらなかった場合のみ、ここに来るようになります。
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');

// 予約フォームを表示（後で作ります）
Route::get('/restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');

// 予約実行（今回実装するメインロジック）
Route::post('/restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('reservations.store');

// 店舗管理ダッシュボード
Route::get('/owner/restaurants/{restaurant}/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
require __DIR__.'/auth.php';