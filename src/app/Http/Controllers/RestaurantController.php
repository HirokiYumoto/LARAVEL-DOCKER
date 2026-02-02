<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\Prefecture;
use App\Models\City;
use App\Models\Genre;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    /**
     * 店舗一覧表示（検索機能付き）
     */
    public function index(Request $request)
    {
        // ベースのクエリ
        $query = Restaurant::query()->with('city.prefecture');

        // 1. キーワード検索（Meilisearch利用）
        if ($request->filled('keyword')) {
            // Meilisearchで検索し、ヒットしたIDを取得
            // 表記揺れやタイポはここでMeilisearchが吸収してくれる
            $searchResultIds = Restaurant::search($request->keyword)->keys();
            
            // ヒットしたIDの店舗だけに絞り込む
            $query->whereIn('id', $searchResultIds);
        }

        // 2. エリア選択（プルダウン）による絞り込み
        // ※キーワード検索の結果の中から、さらにエリアで絞り込めます
        if ($request->filled('prefecture_id')) {
            $query->whereHas('city', function($q) use ($request) {
                $q->where('prefecture_id', $request->prefecture_id);
            });
        }

        $restaurants = $query->latest()->get();
        $prefectures = Prefecture::all();

        return view('restaurants.index', compact('restaurants', 'prefectures'));
    }
    /**
     * 店舗作成画面の表示
     */
    public function create()
    {
        // 市区町村データも一緒に取得してパフォーマンス向上
        $prefectures = Prefecture::with('cities')->get();
        $genres = Genre::all();

        return view('restaurants.create', compact('prefectures', 'genres'));
    }

    /**
     * 店舗保存処理
     */
    public function store(Request $request)
    {
        // 1. 入力チェック
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:255', // ★追加：必須項目
            'nearest_station' => 'nullable|string|max:255',
            'menu_info' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048', 
        ]);

        // 2. 店舗データの保存
        $restaurant = Restaurant::create([
            'name' => $request->name,
            'description' => $request->description,
            'city_id' => $request->city_id,
            'address' => $request->address,                 // ★追加
            'nearest_station' => $request->nearest_station,
            'menu_info' => $request->menu_info,
            'user_id' => auth()->id(),
        ]);

        // 3. 画像の保存処理
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('restaurant_images', 'public');
                RestaurantImage::create([
                    'restaurant_id' => $restaurant->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', '店舗を登録しました！');
    }

    public function show($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return view('restaurants.show', compact('restaurant'));
    }
    
    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        if ($restaurant->user_id !== auth()->id()) {
            abort(403, '権限がありません。');
        }
        $restaurant->delete();
        return to_route('dashboard')->with('success', '店舗を削除しました。');
    }
}