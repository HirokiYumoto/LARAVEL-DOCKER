<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // お気に入り登録
    public function store($restaurantId)
    {
        $user = Auth::user();

        // 重複チェック：まだ登録していなければ作成する
        if (!$user->favorites()->where('restaurant_id', $restaurantId)->exists()) {
            // ★修正ポイント：attach() ではなく create() を使います
            $user->favorites()->create([
                'restaurant_id' => $restaurantId
            ]);
        }

        return back(); // 元の画面に戻る
    }

    // お気に入り解除
    public function destroy($restaurantId)
    {
        $user = Auth::user();

        // ★修正ポイント：detach() ではなく delete() を使います
        $user->favorites()->where('restaurant_id', $restaurantId)->delete();

        return back();
    }
}