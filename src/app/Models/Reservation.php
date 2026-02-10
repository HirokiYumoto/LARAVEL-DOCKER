<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'restaurant_seat_type_id', // 追加
        'reserved_at',
        'end_at', // 追加
        'number_of_people',
    ];

    // 日付カラムをCarbonインスタンス（日付操作ライブラリ）として扱う
    protected $casts = [
        'reserved_at' => 'datetime',
        'end_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // リレーション：どの席タイプで予約したか
    public function seatType()
    {
        // 外部キーが 'restaurant_seat_type_id' なので明示的に指定する場合もありますが、
        // Laravelの命名規則通りなら自動判定されます。念のため belongsTo の第2引数は省略可。
        return $this->belongsTo(RestaurantSeatType::class, 'restaurant_seat_type_id');
    }
}