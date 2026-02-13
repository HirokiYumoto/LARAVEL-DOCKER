<x-app-layout>
    <x-site-header />

    <main class="py-10">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow">

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
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reservations.store', $restaurant->id) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">日付</label>
                        <input type="date" name="reservation_date" value="{{ old('reservation_date') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">時間</label>
                        <input type="time" name="reservation_time" value="{{ old('reservation_time') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">席タイプ</label>
                        <select name="seat_type_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="">選択してください</option>
                            @foreach ($seatTypes as $seatType)
                                <option value="{{ $seatType->id }}" {{ old('seat_type_id') == $seatType->id ? 'selected' : '' }}>
                                    {{ $seatType->name }}（定員: {{ $seatType->capacity }}）
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">人数</label>
                        <input type="number" name="number_of_people" min="1" value="{{ old('number_of_people', 1) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>

                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-full transition shadow-md">
                        予約を確定する
                    </button>
                </form>

            </div>
        </div>
    </main>
</x-app-layout>
