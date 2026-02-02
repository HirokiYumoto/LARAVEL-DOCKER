<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $restaurant->name }} - tabelogg</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <x-site-header />

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-20">
            
            {{-- 戻るリンク --}}
            <div class="mb-6">
                <a href="{{ route('restaurants.index') }}" class="text-blue-600 hover:underline">← 一覧に戻る</a>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8 lg:p-12">
                
                {{-- 1. 店名とエリア情報 --}}
                <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-8 border-b pb-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $restaurant->name }}</h1>
                        <div class="flex flex-col gap-1 text-lg text-gray-500">
                            {{-- ★★★ 追加：エリアと住所を連結して表示 ★★★ --}}
                            <p class="flex items-center gap-1">
                                📍 {{ $restaurant->city->prefecture->name }}{{ $restaurant->city->name }}{{ $restaurant->address }}
                            </p>
                            
                            @if($restaurant->nearest_station)
                                <p class="flex items-center gap-1 text-base text-gray-600">
                                    🚃 {{ $restaurant->nearest_station }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- お気に入りボタン --}}
                    @auth
                        <div class="mt-4 md:mt-0">
                            @if($restaurant->favorites()->where('user_id', Auth::id())->exists())
                                <form action="{{ route('favorites.destroy', $restaurant->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 px-5 py-2 rounded-full font-bold flex items-center gap-2 transition">
                                        ❤️ お気に入り解除
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('favorites.store', $restaurant->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-gray-100 text-gray-500 hover:text-red-500 hover:bg-red-50 border border-gray-200 px-5 py-2 rounded-full font-bold flex items-center gap-2 transition">
                                        🤍 お気に入り登録
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>

                {{-- 2. 左右2カラムレイアウト --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-12">
                    
                    {{-- 左半分：お店の紹介 + メニュー情報 --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="text-orange-500">📖</span> お店の紹介
                            </h3>
                            <div class="bg-gray-50 p-6 rounded-lg text-gray-700 leading-relaxed whitespace-pre-wrap h-full">{{ $restaurant->description }}</div>
                        </div>

                        @if($restaurant->menu_info)
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                                    <span class="text-orange-500">🥢</span> メニュー・価格
                                </h3>
                                <div class="bg-orange-50 border border-orange-100 p-6 rounded-lg text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $restaurant->menu_info }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- 右半分：店舗画像 --}}
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="text-orange-500">📷</span> 店舗・メニュー画像
                        </h3>
                        
                        @if($restaurant->images->isNotEmpty())
                            @php
                                $allImages = $restaurant->images->map(fn($img) => asset('storage/' . $img->image_path));
                                $firstImage = $restaurant->images->first();
                            @endphp

                            <div class="aspect-video w-full bg-gray-100 rounded-lg overflow-hidden border border-gray-200 relative group cursor-pointer shadow-sm js-modal-trigger"
                                 data-images="{{ json_encode($allImages) }}"
                                 onclick="openModalFromElement(this)">
                                
                                <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                     class="w-full h-full object-contain transition duration-300"
                                     alt="店舗画像">
                                     
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition"></div>
                            </div>
                        @else
                            <div class="aspect-video bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 border border-gray-200 border-dashed">
                                画像は登録されていません
                            </div>
                        @endif
                    </div>
                </div>

                {{-- レビューエリア --}}
                <div class="border-t pt-10">
                    <h2 class="text-2xl font-bold mb-8">みんなの口コミ</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        {{-- 投稿フォーム --}}
                        <div>
                            @auth
                                <div class="bg-orange-50 p-6 rounded-lg border border-orange-100">
                                    <h3 class="font-bold mb-4 text-orange-800">レビューを投稿する</h3>
                                    <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-bold mb-1 text-gray-700">評価</label>
                                            <select name="rating" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                                <option value="5">⭐️⭐️⭐️⭐️⭐️ 5 - とても良い</option>
                                                <option value="4">⭐️⭐️⭐️⭐️ 4 - 良い</option>
                                                <option value="3" selected>⭐️⭐️⭐️ 3 - 普通</option>
                                                <option value="2">⭐️⭐️ 2 - いまいち</option>
                                                <option value="1">⭐️ 1 - 悪い</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-bold mb-1 text-gray-700">コメント</label>
                                            <textarea name="comment" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="美味しかった！など"></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-bold mb-1 text-gray-700">画像（複数可）</label>
                                            <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-200 file:text-orange-800 hover:file:bg-orange-300 cursor-pointer">
                                        </div>
                                        <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-full hover:bg-orange-600 w-full transition shadow-md">投稿する</button>
                                    </form>
                                </div>
                            @else
                                <div class="bg-gray-100 p-6 rounded text-center text-gray-500">
                                    <p class="mb-2">レビューを投稿するにはログインしてください。</p>
                                    <a href="{{ route('login') }}" class="text-orange-500 font-bold underline">ログインする</a>
                                </div>
                            @endauth
                        </div>

                        {{-- レビュー一覧 --}}
                        <div>
                            @if($restaurant->reviews->isEmpty())
                                <p class="text-gray-500 text-center py-10 bg-gray-50 rounded-lg">まだ口コミはありません。<br>最初の投稿者になりましょう！</p>
                            @else
                                <div class="space-y-6">
                                    @foreach($restaurant->reviews as $review)
                                        <div class="border-b border-gray-100 pb-6 last:border-0">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="font-bold text-gray-800 flex items-center gap-2">
                                                    👤 {{ $review->user->name }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $review->created_at->format('Y/m/d') }}</span>
                                            </div>
                                            <div class="flex text-sm mb-2 text-yellow-500">
                                                {{ str_repeat('★', $review->rating) }}<span class="text-gray-300">{{ str_repeat('★', 5 - $review->rating) }}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm leading-relaxed mb-3 bg-white p-3 rounded border border-gray-50 shadow-sm">{{ $review->comment }}</p>
                                            
                                            @if($review->images->isNotEmpty())
                                                @php
                                                    $reviewImages = $review->images->map(fn($img) => asset('storage/' . $img->image_path));
                                                    $firstReviewImage = $review->images->first();
                                                @endphp

                                                <div class="mt-3 w-full md:w-3/4 aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-200 relative group cursor-pointer shadow-sm js-modal-trigger"
                                                     data-images="{{ json_encode($reviewImages) }}"
                                                     onclick="openModalFromElement(this)">
                                                    <img src="{{ asset('storage/' . $firstReviewImage->image_path) }}" class="w-full h-full object-contain bg-gray-50" alt="レビュー画像">
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition"></div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- モーダル --}}
    <div id="image-modal" class="fixed inset-0 z-50 bg-black/95 hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeModal()">
        <button onclick="closeModal()" class="fixed top-6 right-6 z-[60] bg-white rounded-full p-3 text-black shadow-lg hover:bg-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <button id="prev-btn" onclick="prevImage()" class="hidden fixed left-4 top-1/2 -translate-y-1/2 z-[60] bg-white rounded-full p-4 text-black shadow-lg hover:bg-gray-200 transition transform hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <div class="relative w-full max-w-7xl h-full flex items-center justify-center mx-auto pointer-events-none">
            <img id="modal-image" src="" class="max-w-full max-h-[90vh] object-contain select-none shadow-2xl rounded pointer-events-auto">
        </div>
        <button id="next-btn" onclick="nextImage()" class="hidden fixed right-4 top-1/2 -translate-y-1/2 z-[60] bg-white rounded-full p-4 text-black shadow-lg hover:bg-gray-200 transition transform hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <script>
        let currentImages = [];
        let currentIndex = 0;
        function openModalFromElement(element) {
            const imagesJson = element.getAttribute('data-images');
            if(imagesJson) {
                const images = JSON.parse(imagesJson);
                openModal(images, 0); 
            }
        }
        function openModal(images, index = 0) {
            if(!images || images.length === 0) return;
            currentImages = images;
            currentIndex = index;
            updateImage();
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            if (currentImages.length > 1) {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
            }
            document.getElementById('image-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() { 
            document.getElementById('image-modal').classList.add('hidden'); 
            document.body.style.overflow = 'auto';
        }
        function updateImage() { 
            document.getElementById('modal-image').src = currentImages[currentIndex];
        }
        function nextImage() { currentIndex = (currentIndex + 1) % currentImages.length; updateImage(); }
        function prevImage() { currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length; updateImage(); }
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('image-modal');
            if (modal.classList.contains('hidden')) return;
            if (currentImages.length > 1) {
                if (e.key === 'ArrowRight') nextImage();
                if (e.key === 'ArrowLeft') prevImage();
            }
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>