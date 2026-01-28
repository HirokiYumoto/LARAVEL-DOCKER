<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ★ここを追加
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory; // ★ここを追加

    // どの都道府県に属しているか
    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }
}