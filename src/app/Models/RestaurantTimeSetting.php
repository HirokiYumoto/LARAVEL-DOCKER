<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTimeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'day_of_week',
        'start_time',
        'end_time',
        'stay_minutes',
    ];

    // リレーション：この設定は、ある店舗に属している
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}