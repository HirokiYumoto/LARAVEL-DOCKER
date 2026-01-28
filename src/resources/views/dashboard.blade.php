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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2">マイページ</h1>
                    <p class="text-gray-600">
                        ようこそ、<span class="font-semibold text-lg">{{ Auth::user()->name }}</span> さん！
                    </p>
                    <p class="text-sm text-gray-500 mt-1">メールアドレス: {{ Auth::user()->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <span class="text-red-500 mr-2">♥</span> お気に入りのお店
                    </h2>

                    @if($favorites->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <p class="mb-4">まだお気に入りのお店がありません。</p>
                            <a href="{{ route('restaurants.index') }}" class="text-orange-500 hover:underline">
                                お店を探しに行く →
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($favorites as $restaurant)
                                <div class="border rounded-lg p-4 flex items-start hover:bg-gray-50 transition">
                                    <div class="h-16 w-16 bg-gray-200 rounded flex-shrink-0 flex items-center justify-center text-xl mr-4">
                                        🍜
                                    </div>
                                    
                                    <div>
                                        <h3 class="font-bold text-gray-800 mb-1">
                                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="hover:text-orange-500">
                                                {{ $restaurant->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-gray-500 mb-1">
                                            {{ $restaurant->city->prefecture->name }} {{ $restaurant->city->name }}
                                        </p>
                                        <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-xs text-blue-600 hover:underline">
                                            詳細を見る
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <span class="text-yellow-500 mr-2">★</span> 投稿したレビュー
                    </h2>
                    
                    @if($reviews->isEmpty())
                        <p class="text-gray-500 text-sm">
                            （まだレビューはありません）
                        </p>
                    @else
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border-b pb-4 last:border-b-0 last:pb-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-bold text-gray-800">
                                            <a href="{{ route('restaurants.show', $review->restaurant->id) }}" class="hover:text-orange-500 transition">
                                                {{ $review->restaurant->name }}
                                            </a>
                                        </h3>
                                        <span class="text-xs text-gray-500">{{ $review->created_at->format('Y/m/d') }}</span>
                                    </div>

                                    <div class="flex text-sm mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <span class="text-yellow-500">★</span>
                                            @else
                                                <span class="text-gray-300">★</span>
                                            @endif
                                        @endfor
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-3 rounded">
                                        {{ $review->comment }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                    <h2 class="text-lg font-bold mb-4 text-gray-700">アカウント設定</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('profile.edit') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">
                            プロフィール編集
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold py-2 px-4 transition">
                                ログアウト
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </main>
    
    <footer class="bg-white border-t mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; 2026 Laravel Ramen App. All rights reserved.
        </div>
    </footer>

</body>
</html>s