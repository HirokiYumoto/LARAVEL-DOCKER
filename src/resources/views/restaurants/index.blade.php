<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お店一覧 - tabelogg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <x-site-header />

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-20">
            
            {{-- ★★★ 修正箇所：ヘッダーエリア（タイトル・並び替え・登録ボタン） ★★★ --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-6 gap-4">
                
                {{-- 左側：タイトルと並び替え --}}
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <h2 class="text-xl font-bold text-gray-800 whitespace-nowrap">
                        @if(request('keyword') || request('prefecture_id'))
                            {{-- paginateを使っているため total() で全件数を取得 --}}
                            検索結果: {{ $restaurants->total() }} 件
                        @else
                            すべてのお店
                        @endif
                    </h2>
                    <div class="relative">
    <form id="sortForm" action="{{ route('restaurants.index') }}" method="GET" class="flex items-center">
        {{-- 既存の検索条件を維持するための隠しフィールド --}}
        @if(request('keyword'))
            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
        @endif
        @if(request('prefecture_id'))
            <input type="hidden" name="prefecture_id" value="{{ request('prefecture_id') }}">
        @endif
        @if(request('area'))
            <input type="hidden" name="area" value="{{ request('area') }}">
        @endif
        @if(request('genre'))
            <input type="hidden" name="genre" value="{{ request('genre') }}">
        @endif
        
        {{-- 位置情報送信用の隠しフィールド --}}
        <input type="hidden" name="lat" id="lat_input" value="{{ request('lat') }}">
        <input type="hidden" name="lng" id="lng_input" value="{{ request('lng') }}">

        <select name="sort" id="sort" onchange="handleSortChange(this)" 
                class="text-sm border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 cursor-pointer py-1 pl-3 pr-8">
            <option value="" {{ !request('sort') ? 'selected' : '' }}>新着順</option>
            <option value="nearest" {{ request('sort') === 'nearest' ? 'selected' : '' }}>📍 現在地に近い</option>
            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>⭐️ 評価が高い</option>
            <option value="favorites" {{ request('sort') === 'favorites' ? 'selected' : '' }}>🔖 人気順</option>
            <option value="reviews" {{ request('sort') === 'reviews' ? 'selected' : '' }}>💬 口コミ数</option>
        </select>
    </form>
</div>

                </div>
                
                {{-- 右側：店舗オーナー用ボタン --}}
                @auth
                    @if(Auth::user()->isStoreOwner())
                        <a href="{{ route('restaurants.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition shadow whitespace-nowrap">
                            <span class="mr-1 text-lg">➕</span> 新しいお店を登録
                        </a>
                    @endif
                @endauth
            </div>

            {{-- 店舗一覧リスト --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($restaurants as $restaurant)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col h-full group">
                        
                        {{-- 画像エリア --}}
                        <div class="h-48 bg-gray-200 flex items-center justify-center relative overflow-hidden">
                            @if($restaurant->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $restaurant->images->first()->image_path) }}" 
                                     alt="{{ $restaurant->name }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                            @else
                                <span class="text-4xl">🍜</span>
                            @endif

                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="absolute inset-0 z-10"></a>
                        </div>

                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-gray-800 line-clamp-1">
                                        <a href="{{ route('restaurants.show', $restaurant->id) }}" class="hover:text-orange-500 transition">
                                            {{ $restaurant->name }}
                                        </a>
                                    </h3>
                                    <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full whitespace-nowrap ml-2">
                                        {{ $restaurant->city->prefecture->name }}
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ $restaurant->description }}
                                </p>
                            </div>

                            <div class="flex justify-between items-center border-t pt-3 mt-2">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    {{-- 評価（コントローラーで集計済みの reviews_avg_rating を優先利用し、無ければ計算） --}}
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        {{ number_format($restaurant->reviews_avg_rating ?? $restaurant->reviews->avg('rating') ?? 0, 1) }}
                                    </span>
                                    
                                    {{-- お気に入り数（favorites_count を優先利用） --}}
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                                        {{ $restaurant->favorites_count ?? $restaurant->favorites->count() }}
                                    </span>
                                </div>
                                <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-orange-500 hover:text-orange-600 text-sm font-bold">詳細へ &rarr;</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-500">
                        <p>条件に一致するお店は見つかりませんでした。</p>
                        <a href="{{ route('restaurants.index') }}" class="text-blue-500 hover:underline mt-2 inline-block">すべての表示に戻す</a>
                    </div>
                @endforelse
            </div>

            {{-- ★★★ 修正箇所：ページネーションリンクを追加 ★★★ --}}
            <div class="mt-8">
                {{ $restaurants->links() }}
            </div>
            
        </div>
    </main>

    {{-- ★★★ 修正箇所：並び替え用JavaScript ★★★ --}}
<script>
    function handleSortChange(selectElement) {
        const form = document.getElementById('sortForm');
        const selectedValue = selectElement.value;

        // 「現在地から近い順」が選ばれた場合のみ位置情報を取得
        if (selectedValue === 'nearest') {
            if (navigator.geolocation) {
                // ユーザーに処理中であることを伝える（オプション）
                // selectElement.disabled = true; 
                
                navigator.geolocation.getCurrentPosition(
                    // 成功時
                    function(position) {
                        document.getElementById('lat_input').value = position.coords.latitude;
                        document.getElementById('lng_input').value = position.coords.longitude;
                        form.submit(); // 位置情報をセットしてから送信
                    },
                    // 失敗時
                    function(error) {
                        alert('現在地を取得できませんでした。設定を確認してください。');
                        selectElement.value = ''; // 選択を元に戻す
                    }
                );
            } else {
                alert('お使いのブラウザでは位置情報がサポートされていません。');
                selectElement.value = '';
            }
        } else {
            // それ以外は位置情報をクリアして送信
            document.getElementById('lat_input').value = '';
            document.getElementById('lng_input').value = '';
            form.submit();
        }
    }
</script>
</body>
</html>