<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('店舗情報を編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- エラー表示 --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>・{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- 店舗名 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">店舗名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                        </div>

                        {{-- エリア選択 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">エリア <span class="text-red-500">*</span></label>
                            <select name="city_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                @foreach($prefectures as $prefecture)
                                    <optgroup label="{{ $prefecture->name }}">
                                        @foreach($prefecture->cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $restaurant->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- 住所 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">住所詳細 <span class="text-red-500">*</span></label>
                            <input type="text" name="address" value="{{ old('address', $restaurant->address) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                            <p class="text-xs text-gray-500 mt-1">※市区町村以降の住所を入力してください</p>
                        </div>

                        {{-- 最寄駅 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">最寄駅（任意）</label>
                            <input type="text" name="nearest_station" value="{{ old('nearest_station', $restaurant->nearest_station) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        {{-- お店の紹介 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">お店の紹介 <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>{{ old('description', $restaurant->description) }}</textarea>
                        </div>

                        {{-- メニュー情報 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">おすすめメニュー・価格など（任意）</label>
                            <textarea name="menu_info" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('menu_info', $restaurant->menu_info) }}</textarea>
                        </div>

                        {{-- 座席タイプ --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">座席タイプ（任意）</label>
                            <p class="text-xs text-gray-500 mb-3">予約機能を利用する場合は、座席タイプを追加してください。</p>

                            <div id="seat-types-container">
                                {{-- JavaScript で既存データ + 動的に追加される --}}
                            </div>

                            <button type="button" onclick="addSeatType()" class="mt-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-full transition border border-gray-300">
                                + 座席タイプを追加
                            </button>
                        </div>

                        {{-- 営業時間設定 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">営業時間（任意）</label>
                            <p class="text-xs text-gray-500 mb-3">予約を受け付ける曜日・時間帯を設定してください。</p>

                            @php
                                $days = [
                                    1 => '月曜日', 2 => '火曜日', 3 => '水曜日', 4 => '木曜日',
                                    5 => '金曜日', 6 => '土曜日', 0 => '日曜日', 7 => '祝日',
                                ];
                                $existingSettings = $restaurant->timeSettings->keyBy('day_of_week');
                            @endphp

                            <div class="space-y-3" id="time-settings-container">
                                @foreach($days as $dayNum => $dayName)
                                    @php $hasSetting = $existingSettings->has($dayNum); @endphp
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                                            <input type="checkbox" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500 js-day-toggle" data-day="{{ $dayNum }}"
                                                onchange="toggleDayFields({{ $dayNum }})" {{ $hasSetting ? 'checked' : '' }}>
                                            <span class="font-bold text-sm text-gray-700">{{ $dayName }}</span>
                                        </label>
                                        <div id="day-fields-{{ $dayNum }}" class="{{ $hasSetting ? '' : 'hidden' }} mt-3">
                                            <input type="hidden" name="time_settings[{{ $dayNum }}][day_of_week]" value="{{ $dayNum }}" {{ $hasSetting ? '' : 'disabled' }}>
                                            <div class="grid grid-cols-3 gap-3">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">開始時間</label>
                                                    <select name="time_settings[{{ $dayNum }}][start_time]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" {{ $hasSetting ? '' : 'disabled' }}>
                                                        @for($h = 0; $h < 24; $h++)
                                                            @for($m = 0; $m < 60; $m += 15)
                                                                @php $timeVal = sprintf('%02d:%02d', $h, $m); @endphp
                                                                <option value="{{ $timeVal }}" {{ $hasSetting && substr($existingSettings[$dayNum]->start_time, 0, 5) === $timeVal ? 'selected' : '' }}>{{ $timeVal }}</option>
                                                            @endfor
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">終了時間</label>
                                                    <select name="time_settings[{{ $dayNum }}][end_time]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" {{ $hasSetting ? '' : 'disabled' }}>
                                                        @for($h = 0; $h < 24; $h++)
                                                            @for($m = 0; $m < 60; $m += 15)
                                                                @php $timeVal = sprintf('%02d:%02d', $h, $m); @endphp
                                                                <option value="{{ $timeVal }}" {{ $hasSetting && substr($existingSettings[$dayNum]->end_time, 0, 5) === $timeVal ? 'selected' : '' }}>{{ $timeVal }}</option>
                                                            @endfor
                                                        @endfor
                                                        <option value="24:00" {{ $hasSetting && substr($existingSettings[$dayNum]->end_time, 0, 5) === '24:00' ? 'selected' : '' }}>24:00</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">滞在時間</label>
                                                    <select name="time_settings[{{ $dayNum }}][stay_minutes]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" {{ $hasSetting ? '' : 'disabled' }}>
                                                        <option value="30" {{ $hasSetting && $existingSettings[$dayNum]->stay_minutes == 30 ? 'selected' : '' }}>30分</option>
                                                        <option value="60" {{ $hasSetting && $existingSettings[$dayNum]->stay_minutes == 60 ? 'selected' : '' }}>60分</option>
                                                        <option value="90" {{ $hasSetting && $existingSettings[$dayNum]->stay_minutes == 90 ? 'selected' : '' }}>90分</option>
                                                        <option value="120" {{ $hasSetting && $existingSettings[$dayNum]->stay_minutes == 120 ? 'selected' : '' }}>120分</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 既存画像 --}}
                        @if($restaurant->images->isNotEmpty())
                            <div class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">登録済み画像</label>
                                {{-- TODO(human): 既存画像の表示・削除UIを実装 --}}
                                <div class="flex flex-wrap gap-3">
                                    @foreach($restaurant->images as $img)
                                        <div class="w-24 h-24 rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">※画像の削除機能は現在準備中です</p>
                            </div>
                        @endif

                        {{-- 画像追加アップロード --}}
                        <div class="mb-8">
                            <label class="block text-gray-700 font-bold mb-2">画像を追加（複数可）</label>
                            <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-200 file:text-orange-800 hover:file:bg-orange-300 cursor-pointer">
                        </div>

                        <div class="flex justify-center gap-4">
                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition">キャンセル</a>
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-8 rounded-full shadow-lg transition">更新する</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        let seatTypeIndex = 0;
        const inputClass = 'w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500';

        // 既存の座席タイプを初期表示
        const existingSeatTypes = @json($restaurant->seatTypes);
        existingSeatTypes.forEach(st => {
            addSeatType(st.type, st.capacity, st.seats_per_unit);
        });

        function addSeatType(type, capacity, seatsPerUnit) {
            type = type || '';
            capacity = capacity || '';
            seatsPerUnit = seatsPerUnit || '';

            const container = document.getElementById('seat-types-container');
            const card = document.createElement('div');
            card.className = 'border border-gray-200 rounded-lg p-4 mb-3 bg-gray-50';
            card.id = 'seat-type-row-' + seatTypeIndex;
            const idx = seatTypeIndex;
            card.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-bold text-gray-700">席タイプ ${idx + 1}</span>
                    <button type="button" onclick="removeSeatType(${idx})"
                        class="text-red-400 hover:text-red-600 font-bold text-sm">削除</button>
                </div>
                <div class="mb-3">
                    <select name="seat_types[${idx}][type]" onchange="onSeatTypeChange(${idx})"
                        class="${inputClass}" required>
                        <option value="">種類を選択</option>
                        <option value="counter" ${type === 'counter' ? 'selected' : ''}>カウンター</option>
                        <option value="table" ${type === 'table' ? 'selected' : ''}>テーブル</option>
                    </select>
                </div>
                <div id="seat-type-fields-${idx}"></div>
            `;
            container.appendChild(card);
            seatTypeIndex++;

            // 既存データがあれば即座にフィールドを生成
            if (type) {
                onSeatTypeChange(idx, capacity, seatsPerUnit);
            }
        }

        function onSeatTypeChange(idx, capacity, seatsPerUnit) {
            capacity = capacity || '';
            seatsPerUnit = seatsPerUnit || '';
            const fieldsDiv = document.getElementById('seat-type-fields-' + idx);
            const row = document.getElementById('seat-type-row-' + idx);
            const type = row.querySelector('select[name="seat_types[' + idx + '][type]"]').value;

            if (type === 'counter') {
                fieldsDiv.innerHTML = `
                    <label class="block text-sm text-gray-600 mb-1">カウンター席数（合計）</label>
                    <input type="number" name="seat_types[${idx}][capacity]" min="1"
                        class="${inputClass}" placeholder="例：10" value="${capacity}" required>
                    <input type="hidden" name="seat_types[${idx}][seats_per_unit]" value="1">
                `;
            } else if (type === 'table') {
                fieldsDiv.innerHTML = `
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">1卓あたりの席数</label>
                            <input type="number" name="seat_types[${idx}][seats_per_unit]" min="1"
                                class="${inputClass}" placeholder="例：4" value="${seatsPerUnit}" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">テーブル数（卓数）</label>
                            <input type="number" name="seat_types[${idx}][capacity]" min="1"
                                class="${inputClass}" placeholder="例：5" value="${capacity}" required>
                        </div>
                    </div>
                `;
            } else {
                fieldsDiv.innerHTML = '';
            }
        }

        function removeSeatType(index) {
            const row = document.getElementById('seat-type-row-' + index);
            if (row) row.remove();
        }

        function toggleDayFields(dayNum) {
            const fields = document.getElementById('day-fields-' + dayNum);
            const inputs = fields.querySelectorAll('select, input[type="hidden"]');
            const checkbox = document.querySelector('.js-day-toggle[data-day="' + dayNum + '"]');
            if (checkbox.checked) {
                fields.classList.remove('hidden');
                inputs.forEach(el => el.disabled = false);
            } else {
                fields.classList.add('hidden');
                inputs.forEach(el => el.disabled = true);
            }
        }
    </script>
</x-app-layout>
