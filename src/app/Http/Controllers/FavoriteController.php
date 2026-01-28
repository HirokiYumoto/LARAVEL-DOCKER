<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // お気に入り登録（ハートを押した時）
    public function store($restaurantId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 中間テーブルに紐付けを保存（attach）
        if (!$user->favorites()->where('restaurant_id', $restaurantId)->exists()) {
            $user->favorites()->attach($restaurantId);
        }

        return back(); // 元の画面に戻る
    }

    // お気に入り解除（もう一度押した時）
    public function destroy($restaurantId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 中間テーブルから紐付けを削除（detach）
        $user->favorites()->detach($restaurantId);

        return back(); // 元の画面に戻る
    }
}