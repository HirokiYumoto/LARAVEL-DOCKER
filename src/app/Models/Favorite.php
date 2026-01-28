<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // お気に入り登録
    public function store($restaurantId)
    {
        $user = Auth::user();

        // ★修正ポイント： 'restaurant_id' ではなく 'id' でチェックする
        if (!$user->favorites()->where('id', $restaurantId)->exists()) {
            $user->favorites()->attach($restaurantId);
        }

        return back();
    }

    // お気に入り解除
    public function destroy($restaurantId)
    {
        $user = Auth::user();
        $user->favorites()->detach($restaurantId);

        return back();
    }
}