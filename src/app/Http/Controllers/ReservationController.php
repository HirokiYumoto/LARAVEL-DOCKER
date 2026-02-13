<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\RestaurantSeatType;
use App\Models\RestaurantTimeSetting;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * マイ予約一覧
     */
    public function index()
    {
        $reservations = Auth::user()->reservations()
            ->with(['restaurant', 'seatType'])
            ->orderBy('reserved_at', 'desc')
            ->get();

        $upcoming = $reservations->where('reserved_at', '>=', now());
        $past = $reservations->where('reserved_at', '<', now());

        return view('reservations.index', compact('upcoming', 'past'));
    }

    /**
     * 予約フォーム表示
     */
    public function create(Restaurant $restaurant)
    {
        $seatTypes = $restaurant->seatTypes;
        return view('reservations.create', compact('restaurant', 'seatTypes'));
    }

    /**
     * 予約実行ロジック
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        // 1. 入力値のバリデーション
        $request->validate([
            'seat_type_id' => 'required|exists:restaurant_seat_types,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|integer|min:1',
        ]);

        // 予約開始日時をCarbonインスタンス化
        $startDateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time);
        $dayOfWeek = $startDateTime->dayOfWeek;

        // 2. その時間の「滞在時間ルール」を取得
        $timeSetting = RestaurantTimeSetting::where('restaurant_id', $restaurant->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $request->reservation_time)
            ->where('end_time', '>=', $request->reservation_time)
            ->first();

        if (!$timeSetting) {
            return back()->withErrors(['time' => '指定された時間は予約を受け付けていません。']);
        }

        // 3. 終了時間を計算
        $endDateTime = $startDateTime->copy()->addMinutes($timeSetting->stay_minutes);

        // 4. 空席チェック
        $seatType = RestaurantSeatType::find($request->seat_type_id);
        $capacity = $seatType->capacity;

        $existingReservationsCount = Reservation::where('restaurant_id', $restaurant->id)
            ->where('restaurant_seat_type_id', $seatType->id)
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('reserved_at', '<', $endDateTime)
                      ->where('end_at', '>', $startDateTime);
            })
            ->count();

        // 5. 判定 & 保存
        if ($existingReservationsCount >= $capacity) {
            return back()->withErrors(['error' => '申し訳ありません。その時間は満席です。']);
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurant->id,
            'restaurant_seat_type_id' => $seatType->id,
            'reserved_at' => $startDateTime,
            'end_at' => $endDateTime,
            'number_of_people' => $request->number_of_people,
        ]);

        return redirect()->route('restaurants.show', $restaurant->id)
            ->with('success', '予約が完了しました！');
    }

    /**
     * 予約キャンセル（本人のみ）
     */
    public function destroy(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', '予約をキャンセルしました。');
    }
}
