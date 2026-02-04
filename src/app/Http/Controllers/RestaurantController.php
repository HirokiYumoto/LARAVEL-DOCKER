<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\Prefecture;
use App\Models\City;
use App\Models\Genre;
use Illuminate\Support\Facades\Storage;
// ★★★ ↓この1行を必ず追加してください！ ★★★
use Illuminate\Support\Facades\Http; 

class RestaurantController extends Controller
{
    // ...
    /**
     * 店舗一覧表示（検索機能・並び替え機能付き）
     */
    public function index(Request $request)
    {
        // ベースのクエリ
        // ★並び替えに必要なデータ（評価平均、レビュー数、お気に入り数）を事前に計算しておく
        $query = Restaurant::query()
            ->with('city.prefecture')
            ->withAvg('reviews', 'rating')  // reviews_avg_rating が利用可能になる
            ->withCount('reviews')          // reviews_count が利用可能になる
            ->withCount('favorites');       // favorites_count が利用可能になる

        // 1. キーワード検索（Meilisearch利用：既存機能）
        if ($request->filled('keyword')) {
            // Meilisearchで検索し、ヒットしたIDを取得
            $searchResultIds = Restaurant::search($request->keyword)->keys();
            
            // ヒットしたIDの店舗だけに絞り込む
            $query->whereIn('id', $searchResultIds);
        }

        // 2. エリア選択（プルダウン）による絞り込み（既存機能）
        if ($request->filled('prefecture_id')) {
            $query->whereHas('city', function($q) use ($request) {
                $q->where('prefecture_id', $request->prefecture_id);
            });
        }

        // 3. 並び替えロジック（★今回追加した機能）
        $sort = $request->input('sort');
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        switch ($sort) {
            case 'nearest':
                // 現在地から近い順（位置情報がある場合のみ）
                if ($lat && $lng) {
                    // 球面三角法で距離を計算し、distanceという名前で取得して並び替え
                    $query->select('*')
                        ->selectRaw(
                            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                            [$lat, $lng, $lat]
                        )
                        ->orderBy('distance');
                }
                break;

            case 'rating':
                // 評価が高い順
                $query->orderByDesc('reviews_avg_rating');
                break;

            case 'favorites':
                // お気に入り数が多い順
                $query->orderByDesc('favorites_count');
                break;

            case 'reviews':
                // 口コミ数が多い順
                $query->orderByDesc('reviews_count');
                break;

            default:
                // デフォルトは新着順（既存機能）
                $query->latest();
                break;
        }

        // ページネーション（検索条件を維持するためのappendsを追加）
        // ※データ量が増えると get() だと重くなるため paginate(12) に変更しています
        $restaurants = $query->paginate(12)->appends($request->all());
        
        $prefectures = Prefecture::all();

        return view('restaurants.index', compact('restaurants', 'prefectures'));
    }
/**
     * 店舗詳細画面を表示
     */
    public function show($id)
    {
        // IDに紐づく店舗情報を取得（見つからなければ404エラー）
        // リレーション（レビュー、レビュー画像、レビュー投稿者）もまとめて取得
        $restaurant = \App\Models\Restaurant::with([
            'reviews.user',   // レビューの投稿者
            'reviews.images'  // レビューの画像
            // 'categories',  // ジャンル機能が実装済みならここに追加
        ])->findOrFail($id);

        return view('restaurants.show', compact('restaurant'));
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
   /**
     * 店舗保存処理（OpenStreetMap版）
     */
/**
     * 店舗保存処理（完成版）
     */
    public function store(Request $request)
    {
        // 1. 入力チェック
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:255',
            'nearest_station' => 'nullable|string|max:255',
            'menu_info' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048', 
        ]);

        // 2. OpenStreetMapから座標を取得
        $latitude = null;
        $longitude = null;

        try {
            // 住所の組み立て
            $city = City::with('prefecture')->find($request->city_id);
            $fullAddress = $city->prefecture->name . $city->name . $request->address;

            // APIリクエスト
            $response = Http::withHeaders([
                // ★成功したUser-Agent設定を採用
                'User-Agent' => 'LaravelApp/1.0 (test-user)' 
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $fullAddress,
                'format' => 'json',
                'limit' => 1,
            ]);

            // データがあれば座標を取り出す
            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                $latitude = $data['lat'];
                $longitude = $data['lon'];
            }

        } catch (\Exception $e) {
            // エラー時はログに残して続行
            \Log::error('Geocoding Error: ' . $e->getMessage());
        }

        // 3. データベースへ保存
        $restaurant = Restaurant::create([
            'name' => $request->name,
            'description' => $request->description,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'nearest_station' => $request->nearest_station,
            'menu_info' => $request->menu_info,
            'user_id' => auth()->id(),
            // ★取得した座標を保存（取れなかった場合はnull）
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        // 4. 画像の保存
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