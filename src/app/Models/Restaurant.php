<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable; // ★追加

class Restaurant extends Model
{
    use HasFactory, Searchable; // ★Searchableを追加

    protected $fillable = [
        'name',
        'description',
        'menu_info',
        'nearest_station',
        'city_id',
        'address',
        'user_id',
    ];

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
     * ★検索エンジン（Meilisearch）に保存するデータ構造を定義
     */
    public function toSearchableArray()
    {
        // リレーション（City, Prefecture）を事前にロード
        $this->load('city.prefecture');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'nearest_station' => $this->nearest_station,
            'menu_info' => $this->menu_info,
            // 検索できるように、関連テーブルの文字列もフラットに持たせる
            'city_name' => $this->city ? $this->city->name : '',
            'prefecture_name' => $this->city && $this->city->prefecture ? $this->city->prefecture->name : '',
        ];
    }
}