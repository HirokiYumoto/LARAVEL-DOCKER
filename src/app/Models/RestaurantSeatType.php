<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantSeatType extends Model
{
    use HasFactory;

    // 保存を許可するカラム
    protected $fillable = [
        'restaurant_id',
        'name',
        'capacity',
    ];

    // リレーション：この席タイプは、ある店舗に属している
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // リレーション：この席タイプには、複数の予約が入る
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
