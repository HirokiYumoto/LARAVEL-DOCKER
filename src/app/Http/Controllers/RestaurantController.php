<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant; // Restaurantモデルを使うよ！という宣言

class RestaurantController extends Controller
{
    public function index(Request $request) // Requestを使えるように引数を追加
    {
        // 1. クエリの準備（まだデータは取らない）
        $query = Restaurant::query()->with('city.prefecture');

        // 2. 検索機能：都道府県が選択されていたら絞り込む
        if ($request->filled('prefecture_id')) {
            // whereHas: リレーション先のテーブル（city）の条件で検索する
            $query->whereHas('city', function($q) use ($request) {
                $q->where('prefecture_id', $request->prefecture_id);
            });
        }

        // 3. 検索機能：キーワードがあれば（店名や説明文で）絞り込む
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhereHas('city', function($q) use ($keyword) {
                      // 市区町村名でも検索できるようにする
                      $q->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        // データの取得
        $restaurants = $query->latest()->get();

        // 4. 検索フォーム用に全都道府県を取得
        $prefectures = \App\Models\Prefecture::all();

        // ビューに渡す（$prefecturesを追加）
        return view('restaurants.index', compact('restaurants', 'prefectures'));
    }

// ... indexメソッドの続き ...

    // ★これを追加
    // 特定のIDのお店データを1件取得して表示する
    public function show($id)
    {
        // IDに該当する店を探す。無ければ404エラーを出す
        $restaurant = Restaurant::findOrFail($id);

        return view('restaurants.show', compact('restaurant'));
    }
}