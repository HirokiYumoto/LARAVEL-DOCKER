<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    // 書き込みを許可する項目
    protected $fillable = ['restaurant_id', 'user_id', 'rating', 'comment'];

    // レビューを書いたユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ★これを追加！：レビュー対象のお店
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
    // このレビューに紐づく画像たち
    public function images()
    {
        return $this->hasMany(ReviewImage::class);
    }
}