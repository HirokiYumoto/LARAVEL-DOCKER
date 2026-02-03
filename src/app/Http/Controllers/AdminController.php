<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * 管理者ダッシュボード表示
     */
    public function index()
    {
        // 最新のデータを取得（必要に応じてページネーションに変更可）
        $users = User::latest()->get();
        $restaurants = Restaurant::with('user')->latest()->get();
        $reviews = Review::with(['user', 'restaurant'])->latest()->get();

        return view('admin.dashboard', compact('users', 'restaurants', 'reviews'));
    }

    /**
     * ユーザー削除
     */
    public function destroyUser($id)
    {
        // 自分自身は削除できないようにする
        if ($id == Auth::id()) {
            return back()->with('error', '自分自身は削除できません。');
        }

        User::destroy($id);
        return back()->with('success', 'ユーザーを削除しました。');
    }

    /**
     * 店舗削除
     */
    public function destroyRestaurant($id)
    {
        Restaurant::destroy($id);
        return back()->with('success', '店舗を削除しました。');
    }

    /**
     * レビュー削除
     */
    public function destroyReview($id)
    {
        Review::destroy($id);
        return back()->with('success', 'レビューを削除しました。');
    }
}