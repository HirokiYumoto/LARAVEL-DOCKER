<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ - tabelogg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <x-site-header />

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-20 space-y-8">
            
            <h1 class="text-3xl font-bold text-gray-900 border-b pb-4">マイページ</h1>
            
            {{-- 成功メッセージの表示 --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ▼ 1. 店舗オーナー向け機能エリア ▼ --}}
            @auth
                @if(Auth::user()->isStoreOwner())
                    
                    {{-- 新規登録ボタン --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500 mb-8">
                        <div class="p-6 text-gray-900 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div>
                                <h3 class="font-bold text-lg mb-1">あなたのお店を掲載しませんか？</h3>
                                <p class="text-sm text-gray-500">簡単3ステップでお店を登録できます。</p>
                            </div>
                            <a href="{{ route('restaurants.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-full shadow-md transition flex items-center gap-2 whitespace-nowrap">
                                <span>🏪</span> 新しい店舗を登録する
                            </a>
                        </div>
                    </div>

                    {{-- 自分の店舗一覧 --}}
                    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                        <div class="p-6 bg-orange-50 border-b border-orange-100">
                            <h3 class="font-bold text-lg text-orange-800 flex items-center gap-2">
                                <span>🏠</span> あなたの掲載店舗管理
                            </h3>
                        </div>
                        <div class="p-6">
                            @if(Auth::user()->restaurants->isEmpty())
                                <p class="text-gray-400 text-sm text-center py-4">登録されている店舗はありません。</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach(Auth::user()->restaurants as $myRestaurant)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition flex items-start gap-4 bg-white">
                                            {{-- 画像サムネイル --}}
                                            <div class="w-20 h-20 bg-gray-100 rounded overflow-hidden flex-shrink-0 border border-gray-200">
                                                @if($myRestaurant->images->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $myRestaurant->images->first()->image_path) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-2xl bg-gray-50">🍜</div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-grow">
                                                <h4 class="font-bold text-lg mb-1">
                                                    <a href="{{ route('restaurants.show', $myRestaurant->id) }}" class="hover:text-orange-500 hover:underline">
                                                        {{ $myRestaurant->name }}
                                                    </a>
                                                </h4>
                                                <p class="text-xs text-gray-500 mb-2">
                                                    📍 {{ $myRestaurant->city->prefecture->name }} {{ $myRestaurant->city->name }}
                                                </p>
                                                
                                                <div class="flex items-center gap-2 mt-3">
                                                    {{-- 確認ボタン --}}
                                                    <a href="{{ route('restaurants.show', $myRestaurant->id) }}" class="text-xs bg-gray-800 text-white px-3 py-2 rounded hover:bg-gray-700 transition shadow-sm font-bold">
                                                        確認する
                                                    </a>
                                                    
                                                    {{-- 削除ボタン --}}
                                                    <form action="{{ route('restaurants.destroy', $myRestaurant->id) }}" method="POST" onsubmit="return confirm('本当にこの店舗を削除しますか？\n削除すると元に戻せません。');" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-xs bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 transition shadow-sm font-bold border border-red-700">
                                                            削除する
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                @endif
            @endauth

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- ▼ 2. お気に入り店舗一覧 ▼ --}}
                <div class="bg-white rounded-lg shadow-md h-full overflow-hidden">
                    <div class="p-6 bg-gray-50 border-b border-gray-100">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <span>❤️</span> お気に入り店舗
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(Auth::user()->favorites->isEmpty())
                            <p class="text-gray-400 text-sm text-center py-4">まだお気に入りのお店はありません。</p>
                        @else
                            <ul class="space-y-4">
                                @foreach(Auth::user()->favorites as $favorite)
                                    @if($favorite->restaurant)
                                        <li class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition border border-gray-100">
                                            <div class="flex-1">
                                                <a href="{{ route('restaurants.show', $favorite->restaurant->id) }}" class="font-bold text-blue-600 hover:underline block mb-1">
                                                    {{ $favorite->restaurant->name }}
                                                </a>
                                                <p class="text-xs text-gray-500 bg-gray-100 inline-block px-2 py-1 rounded">
                                                    {{ $favorite->restaurant->city->prefecture->name }} {{ $favorite->restaurant->city->name }}
                                                </p>
                                            </div>
                                            <form action="{{ route('favorites.destroy', $favorite->restaurant->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-white hover:bg-red-500 border border-red-500 px-3 py-1.5 rounded transition">解除</button>
                                            </form>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- ▼ 3. 投稿したレビュー履歴 ▼ --}}
                <div class="bg-white rounded-lg shadow-md h-full overflow-hidden">
                    <div class="p-6 bg-gray-50 border-b border-gray-100">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <span>📝</span> 投稿したレビュー
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(Auth::user()->reviews->isEmpty())
                            <p class="text-gray-400 text-sm text-center py-4">まだ投稿したレビューはありません。</p>
                        @else
                            <ul class="space-y-6">
                                @foreach(Auth::user()->reviews as $review)
                                    @if($review->restaurant)
                                        <li class="border-b border-gray-100 last:border-0 pb-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <a href="{{ route('restaurants.show', $review->restaurant->id) }}" class="font-bold text-sm text-gray-800 hover:text-orange-500">
                                                    {{ $review->restaurant->name }}
                                                </a>
                                                <span class="text-xs text-gray-400">{{ $review->created_at->format('Y/m/d') }}</span>
                                            </div>
                                            <div class="flex items-center text-xs text-yellow-500 mb-2">
                                                {{ str_repeat('★', $review->rating) }}
                                                <span class="text-gray-300">{{ str_repeat('★', 5 - $review->rating) }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 line-clamp-2 bg-gray-50 p-2 rounded mb-2">{{ $review->comment }}</p>
                                            
                                            {{-- ★★★ 追加：レビュー削除ボタン ★★★ --}}
                                            <div class="flex justify-end">
                                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('本当にこのレビューを削除しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 hover:underline">
                                                        🗑️ 削除する
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>