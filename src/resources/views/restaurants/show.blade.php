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
            
            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('restaurants.index') }}" class="text-blue-600 hover:underline">← 一覧に戻る</a>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="h-80 bg-gray-200 flex items-center justify-center relative">
                    <span class="text-6xl">🍜</span>
                    @auth
                        <div class="absolute bottom-4 right-4">
                            @if($restaurant->favoritedBy()->where('user_id', Auth::id())->exists())
                                <form action="{{ route('favorites.destroy', $restaurant->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-white text-red-500 hover:bg-gray-100 px-4 py-2 rounded-full shadow-md font-bold flex items-center transition">
                                        ❤️ お気に入り解除
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('favorites.store', $restaurant->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-white text-gray-400 hover:text-red-500 hover:bg-gray-50 px-4 py-2 rounded-full shadow-md font-bold flex items-center transition">
                                        🤍 お気に入り登録
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>
                
                <div class="p-10 lg:p-16">
                    <h1 class="text-3xl font-bold mb-6">{{ $restaurant->name }}</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                        <div>
                            <p class="text-gray-600 mb-3">
                                <span class="font-bold">エリア：</span> {{ $restaurant->city->prefecture->name }} {{ $restaurant->city->name }}
                            </p>
                            <p class="text-gray-600 mb-3">
                                <span class="font-bold">住所：</span> {{ $restaurant->address_detail }}
                            </p>
                            <p class="text-gray-600">
                                <span class="font-bold">営業時間：</span> {{ $restaurant->open_time }} 〜 {{ $restaurant->close_time }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-md">
                            <p class="text-gray-700 leading-relaxed">{{ $restaurant->description }}</p>
                        </div>
                    </div>

                    <div class="border-t pt-10 mt-6">
                        <h2 class="text-xl font-bold mb-8">みんなの口コミ</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            
                            <div>
                                @auth
                                    <div class="bg-gray-50 p-6 rounded-lg border">
                                        <h3 class="font-bold mb-4">レビューを投稿する</h3>
                                        <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            
                                            <div class="mb-4">
                                                <label class="block text-sm font-bold mb-1">評価</label>
                                                <select name="rating" class="w-full border-gray-300 rounded-md shadow-sm">
                                                    <option value="5">⭐️⭐️⭐️⭐️⭐️ 5 - とても良い</option>
                                                    <option value="4">⭐️⭐️⭐️⭐️ 4 - 良い</option>
                                                    <option value="3" selected>⭐️⭐️⭐️ 3 - 普通</option>
                                                    <option value="2">⭐️⭐️ 2 - いまいち</option>
                                                    <option value="1">⭐️ 1 - 悪い</option>
                                                </select>
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-bold mb-1">コメント</label>
                                                <textarea name="comment" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="美味しかった！など"></textarea>
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-bold mb-1">画像（複数可）</label>
                                                <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                            </div>

                                            <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-4 rounded hover:bg-orange-600 w-full transition">
                                                投稿する
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="bg-gray-100 p-6 rounded text-center text-gray-500">
                                        <p>レビューを投稿するには<br>ログインしてください。</p>
                                        <a href="{{ route('login') }}" class="text-blue-600 underline mt-2 inline-block">ログインする</a>
                                    </div>
                                @endauth
                            </div>

                            <div>
                                @if($restaurant->reviews->isEmpty())
                                    <p class="text-gray-500">まだ口コミはありません。<br>最初の投稿者になりましょう！</p>
                                @else
                                    <div class="space-y-6">
                                        @foreach($restaurant->reviews as $review)
                                            <div class="border-b pb-6">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="font-bold text-gray-800">{{ $review->user->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $review->created_at->format('Y/m/d') }}</span>
                                                </div>
                                                
                                                <div class="flex text-sm mb-2">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $review->rating)
                                                            <span class="text-yellow-500 text-lg">★</span>
                                                        @else
                                                            <span class="text-gray-300 text-lg">★</span>
                                                        @endif
                                                    @endfor
                                                </div>
                                                
                                                <p class="text-gray-700 text-sm leading-relaxed mb-3">{{ $review->comment }}</p>

                                                @if($review->images->isNotEmpty())
                                                    <div class="mt-3">
                                                        @php
                                                            $imageUrls = $review->images->map(function($img) {
                                                                return asset('storage/' . $img->image_path);
                                                            });
                                                        @endphp
                                                        
                                                        <div 
                                                            class="js-modal-trigger relative w-full aspect-video rounded-lg overflow-hidden cursor-pointer group border border-gray-200 shadow-sm"
                                                            data-images="{{ json_encode($imageUrls) }}"
                                                        >
                                                            {{-- 1枚目の画像を表示 --}}
                                                            <img 
                                                                src="{{ asset('storage/' . $review->images->first()->image_path) }}" 
                                                                class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                                                            >
                                                            
                                                            {{-- 2枚以上ある場合のインジケーター --}}
                                                            @if($review->images->count() > 1)
                                                                <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded flex items-center">
                                                                    <span class="mr-1">📷</span> {{ $review->images->count() }}
                                                                </div>
                                                            @endif
                                                        </div>
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
        </div>
    </main>

  <div id="image-modal" class="fixed inset-0 z-50 bg-black/90 hidden flex items-center justify-center p-4">
        
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white transition z-50 p-2">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="relative w-full max-w-7xl h-full flex items-center justify-center mx-auto">
            
            <button onclick="prevImage()" class="absolute left-0 md:left-4 group bg-white/80 hover:bg-white text-black rounded-full p-3 md:p-4 transition z-50 focus:outline-none shadow-lg">
                <svg class="w-6 h-6 md:w-8 md:h-8 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            
            <img id="modal-image" src="" class="w-auto h-auto max-w-full max-h-[80vh] object-contain px-4 md:px-20 select-none drop-shadow-2xl">

            <button onclick="nextImage()" class="absolute right-0 md:right-4 group bg-white/80 hover:bg-white text-black rounded-full p-3 md:p-4 transition z-50 focus:outline-none shadow-lg">
                <svg class="w-6 h-6 md:w-8 md:h-8 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <div class="absolute bottom-4 text-white bg-black/50 px-4 py-1 rounded-full text-sm tracking-widest font-mono border border-white/20">
            <span id="current-index">1</span> / <span id="total-count">1</span>
        </div>
    </div>

    <script>
        let currentImages = []; // 現在表示中の画像リスト
        let currentIndex = 0;   // 今何枚目か

        document.addEventListener('DOMContentLoaded', function() {
            // "js-modal-trigger" クラスを持つ要素をすべて探す
            const triggers = document.querySelectorAll('.js-modal-trigger');
            
            triggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    // data-images属性からJSONデータを取り出す
                    const imagesData = this.getAttribute('data-images');
                    if (imagesData) {
                        const images = JSON.parse(imagesData);
                        openModal(images);
                    }
                });
            });
        });

        // モーダルを開く
        function openModal(images) {
            if (!images || images.length === 0) return;

            currentImages = images;
            currentIndex = 0;
            updateModalImage();

            document.getElementById('image-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // モーダルを閉じる
        function closeModal() {
            document.getElementById('image-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // 画像更新
        function updateModalImage() {
            const imgElement = document.getElementById('modal-image');
            imgElement.src = currentImages[currentIndex];

            document.getElementById('current-index').textContent = currentIndex + 1;
            document.getElementById('total-count').textContent = currentImages.length;
        }

        // 次へ
        function nextImage() {
            if (currentImages.length <= 1) return;
            currentIndex = (currentIndex + 1) % currentImages.length;
            updateModalImage();
        }

        // 前へ
        function prevImage() {
            if (currentImages.length <= 1) return;
            currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
            updateModalImage();
        }

        // キーボード操作
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('image-modal');
            if (modal.classList.contains('hidden')) return;

            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
            if (e.key === 'Escape') closeModal();
        });

        // 背景クリックで閉じる
        document.getElementById('image-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
    </script>

    

</body>
</html>