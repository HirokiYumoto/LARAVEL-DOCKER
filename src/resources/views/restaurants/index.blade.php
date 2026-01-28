<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お店一覧 - tabelogg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <x-site-header />

    {{-- ▼▼▼ 修正：余白を標準的な py-10 に戻しました ▼▼▼ --}}
    <main class="flex-grow py-10">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-20">
            
            {{-- ここにあった「fixed」の検索バーコードは全て削除しました --}}

            @if(request('prefecture_id') || request('keyword'))
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-gray-600 font-bold">
                        🔍 検索条件：
                        @if(request('prefecture_id'))
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-sm mr-2">
                                {{ \App\Models\Prefecture::find(request('prefecture_id'))->name ?? 'エリア指定' }}
                            </span>
                        @endif
                        @if(request('keyword'))
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-sm">
                                "{{ request('keyword') }}"
                            </span>
                        @endif
                    </p>
                    <a href="{{ route('restaurants.index') }}" class="text-sm text-gray-500 hover:text-red-500 underline">
                        条件をクリア
                    </a>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($restaurants as $restaurant)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300 border border-gray-100">
                        <div class="h-48 bg-gray-200 flex items-center justify-center relative group">
                            <span class="text-4xl">🍜</span>
                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition"></a>
                        </div>
                        
                        <div class="p-5">
                            <h2 class="text-lg font-bold mb-1 text-gray-800 truncate">
                                <a href="{{ route('restaurants.show', $restaurant->id) }}" class="hover:text-orange-500 transition">
                                    {{ $restaurant->name }}
                                </a>
                            </h2>
                            <p class="text-xs text-gray-500 mb-3 flex items-center">
                                <span class="mr-1">📍</span>
                                {{ $restaurant->city->prefecture->name }} {{ $restaurant->city->name }}
                            </p>
                            <p class="text-gray-600 text-sm line-clamp-2 mb-4 h-10">
                                {{ $restaurant->description }}
                            </p>
                            
                            <div class="flex justify-end items-center mt-auto">
                                <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-orange-500 font-bold text-xs border border-orange-500 px-3 py-1 rounded-full hover:bg-orange-50 transition">
                                    詳細 →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($restaurants->isEmpty())
                <div class="text-center py-20 bg-white rounded-lg shadow-sm mt-4">
                    <p class="text-gray-500 text-lg">該当するお店が見つかりませんでした。</p>
                    <a href="{{ route('restaurants.index') }}" class="text-orange-500 font-bold hover:underline mt-4 inline-block">全表示に戻す</a>
                </div>
            @endif

        </div>
    </main>

    <footer class="bg-white border-t mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; 2026 Laravel Ramen App. All rights reserved.
        </div>
    </footer>
</body>
</html>