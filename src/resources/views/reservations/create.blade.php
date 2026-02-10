<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $restaurant->name }} - 予約</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
        
        <h1 class="text-2xl font-bold mb-6">{{ $restaurant->name }} 予約フォーム</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>・{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reservations.store', $restaurant->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">日付</label>
                <input type="date" name="reservation_date" value="{{ old('reservation_date') }}" 
                    class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">時間</label>
                <input type="time" name="reservation_time" value="{{ old('reservation_time') }}" 
                    class="w-full border p-2 rounded">
                <p class="text-sm text-gray-500 mt-1">
                    ※ ランチ: 11:00〜14:00 (60分制) <br>
                    ※ ディナー: 17:00〜23:00 (120分制)
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">席タイプ</label>
                <select name="seat_type_id" class="w-full border p-2 rounded">
                    <option value="">選択してください</option>
                    @foreach ($seatTypes as $seatType)
                        <option value="{{ $seatType->id }}" {{ old('seat_type_id') == $seatType->id ? 'selected' : '' }}>
                            {{ $seatType->name }} (定員: {{ $seatType->capacity }})
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">※お一人様でもテーブル席を選ぶとテーブル枠が消費されます</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">人数</label>
                <input type="number" name="number_of_people" min="1" value="{{ old('number_of_people', 1) }}" 
                    class="w-full border p-2 rounded">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700 transition">
                予約を確定する
            </button>
        </form>
    </div>
</body>
</html>