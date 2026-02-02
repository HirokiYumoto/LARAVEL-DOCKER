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
     * 検索エンジン（Meilisearch）に保存するデータ構造を定義
     */
    public function toSearchableArray()
    {
        // リレーションをロード
        $this->load('city.prefecture');

        // フルパスで指定して呼び出す
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

            // ★★★ 修正箇所： ?? '' をつけて null を回避する ★★★
            'name_kana' => $mecab->toKatakana($this->name ?? ''),
            'description_kana' => $mecab->toKatakana($this->description ?? ''),
            'menu_info_kana' => $mecab->toKatakana($this->menu_info ?? ''), // ← ここがエラーの原因でした
            'city_kana' => $this->city ? $mecab->toKatakana($this->city->name) : '',
        ];
    }
}