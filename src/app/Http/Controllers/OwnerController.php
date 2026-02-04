<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Reservation;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function dashboard(Restaurant $restaurant)
    {
        // 今日の日付を取得
        $today = Carbon::today();

        // 未来の予約を取得（過去のものは今回は表示しない）
        // with()を使うことで、user情報とseatType情報を一度に取ってくる（高速化）
        $reservations = Reservation::with(['user', 'seatType'])
            ->where('restaurant_id', $restaurant->id)
            ->where('reserved_at', '>=', $today) // 今日以降
            ->orderBy('reserved_at', 'asc')       // 日付が近い順
            ->get();

        return view('owner.dashboard', compact('restaurant', 'reservations'));
    }
}