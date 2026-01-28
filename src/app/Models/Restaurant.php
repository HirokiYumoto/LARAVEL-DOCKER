<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'city_id',
        'address_detail',
        'phone_number',
        'open_time',
        'close_time',
        'user_id',
    ];

    // リレーション: 都市
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // リレーション: ジャンル
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    // リレーション: オーナー（ユーザー）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // リレーション: お気に入りしてくれているユーザー
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // リレーション: レビュー（ここに追加しました）
    public function reviews()
    {
        return $this->hasMany(Review::class)->orderBy('created_at', 'desc');
    }

} // ← ★この閉じカッコがすべての最後に来るのが正解です！