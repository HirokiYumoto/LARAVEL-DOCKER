<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\RestaurantSeatType;
use App\Models\RestaurantTimeSetting;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * 予約フォーム表示（Viewはまだないので仮置き）
     */
    public function create(Restaurant $restaurant)
    {
        // 店舗に紐づく席タイプを取得してViewに渡す
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
            'reservation_date' => 'required|date', // 例: 2023-12-01
            'reservation_time' => 'required|date_format:H:i', // 例: 19:00
            'number_of_people' => 'required|integer|min:1',
        ]);

        // 予約開始日時をCarbonインスタンス化
        $startDateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time);
        $dayOfWeek = $startDateTime->dayOfWeek; // 0(日)〜6(土)

        // 2. その時間の「滞在時間ルール」を取得
        // (例: 月曜の19:00を含む設定を探す)
        $timeSetting = RestaurantTimeSetting::where('restaurant_id', $restaurant->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $request->reservation_time)
            ->where('end_time', '>=', $request->reservation_time)
            ->first();

        // 設定が見つからない（営業時間外など）場合はエラー
        if (!$timeSetting) {
            return back()->withErrors(['time' => '指定された時間は予約を受け付けていません。']);
        }

        // 3. 終了時間を計算 (開始 + 設定された滞在時間)
        $endDateTime = $startDateTime->copy()->addMinutes($timeSetting->stay_minutes);

        // 4. 空席チェック（重複チェックの魔法の公式）
        // 指定された席タイプの定員を取得
        $seatType = RestaurantSeatType::find($request->seat_type_id);
        $capacity = $seatType->capacity;

        // 時間が被っている予約をカウント
        $existingReservationsCount = Reservation::where('restaurant_id', $restaurant->id)
            ->where('restaurant_seat_type_id', $seatType->id)
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                // (開始 < 既存終了) AND (終了 > 既存開始)
                $query->where('reserved_at', '<', $endDateTime)
                      ->where('end_at', '>', $startDateTime);
            })
            ->count();

        // 5. 判定 & 保存
        if ($existingReservationsCount >= $capacity) {
            return back()->withErrors(['error' => '申し訳ありません。その時間は満席です。']);
        }

        // 予約作成
        Reservation::create([
            'user_id' => 1, // ★仮：ログイン機能実装後は Auth::id() に変更
            'restaurant_id' => $restaurant->id,
            'restaurant_seat_type_id' => $seatType->id,
            'reserved_at' => $startDateTime,
            'end_at' => $endDateTime, // 計算した終了時間を保存
            'number_of_people' => $request->number_of_people,
        ]);

        return redirect()->route('reservations.create', $restaurant->id)
            ->with('success', '予約が完了しました！');
    }
}