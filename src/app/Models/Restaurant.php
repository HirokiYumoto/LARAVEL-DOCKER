<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Restaurant extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'description',
        'menu_info',
        'nearest_station',
        'city_id',
        'address',
        'user_id',
        'latitude',  // ★追加
        'longitude', // ★追加
    ];

    // 店舗の所有者（ユーザー）へのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
        return $this->hasMany(RestaurantImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * 検索エンジン（Meilisearch）に保存するデータ構造を定義
     */
    public function toSearchableArray()
    {
        // リレーションをロード
        $this->load('city.prefecture');

        // MeCabサービスをフルパスで呼び出し
        $mecab = new \App\Services\MecabService();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'nearest_station' => $this->nearest_station,
            'menu_info' => $this->menu_info,
            
            // 通常のエリア情報
            'city_name' => $this->city ? $this->city->name : '',
            'prefecture_name' => $this->city && $this->city->prefecture ? $this->city->prefecture->name : '',

            // ★読み仮名（カタカナ）データ
            'name_kana' => $mecab->toKatakana($this->name ?? ''),
            'description_kana' => $mecab->toKatakana($this->description ?? ''),
            'menu_info_kana' => $mecab->toKatakana($this->menu_info ?? ''),
            'city_kana' => $this->city ? $mecab->toKatakana($this->city->name) : '',
        ];
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // ★追加: 店舗は複数の「席タイプ」を持つ
    public function seatTypes()
    {
        return $this->hasMany(RestaurantSeatType::class);
    }

    // ★追加: 店舗は複数の「時間設定」を持つ
    public function timeSettings()
    {
        return $this->hasMany(RestaurantTimeSetting::class);
    }

}