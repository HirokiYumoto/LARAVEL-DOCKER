<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新しいお店を登録') }}
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

                    <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 店舗名 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">店舗名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="例：中華そば 太郎" required>
                        </div>

                        {{-- エリア選択 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">エリア <span class="text-red-500">*</span></label>
                            <select name="city_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                @foreach($prefectures as $prefecture)
                                    <optgroup label="{{ $prefecture->name }}">
                                        @foreach($prefecture->cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- ★★★ 追加：住所（必須） ★★★ --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">住所詳細 <span class="text-red-500">*</span></label>
                            <input type="text" name="address" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="例：1-2-3 ○○ビル1F" required>
                            <p class="text-xs text-gray-500 mt-1">※市区町村以降の住所を入力してください</p>
                        </div>

                        {{-- 最寄駅 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">最寄駅（任意）</label>
                            <input type="text" name="nearest_station" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="例：JR新宿駅 東口徒歩5分">
                        </div>

                        {{-- お店の紹介 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">お店の紹介 <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="お店のこだわりや特徴を入力してください" required></textarea>
                        </div>

                        {{-- メニュー情報 --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">おすすめメニュー・価格など（任意）</label>
                            <textarea name="menu_info" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="例：&#13;&#10;特製醤油ラーメン 850円&#13;&#10;半チャーハン 300円"></textarea>
                        </div>

                        {{-- 画像アップロード --}}
                        <div class="mb-8">
                            <label class="block text-gray-700 font-bold mb-2">店舗・メニュー画像（複数可）</label>
                            <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-200 file:text-orange-800 hover:file:bg-orange-300 cursor-pointer">
                        </div>

                        <div class="flex justify-center gap-4">
                            <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition">キャンセル</a>
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-8 rounded-full shadow-lg transition">登録する</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>