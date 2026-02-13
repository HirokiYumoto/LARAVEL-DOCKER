<x-app-layout>
    <x-site-header />

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 戻るリンク --}}
            <div class="mb-6">
                <a href="{{ route('restaurants.index') }}" class="text-blue-600 hover:underline">← 一覧に戻る</a>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                
                {{-- ========================================== --}}
                {{-- ★★★ 上部エリア（固定表示） ★★★ --}}
                {{-- ========================================== --}}
                <div class="p-6 lg:p-10 border-b border-gray-100">
                    {{-- 店名と評価エリア --}}
                    <div class="mb-4">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            {{ $restaurant->name }}
                        </h1>

                        <div class="flex items-end gap-6 flex-wrap">
                            {{-- 平均満足度 --}}
                            <div class="text-3xl font-bold text-orange-500 flex items-center">
                                <span class="mr-1 text-2xl">★</span>
                                {{ number_format($restaurant->reviews->avg('rating') ?? 0, 1) }}
                            </div>

                            {{-- 口コミ数とお気に入り数 --}}
                            <div class="text-base text-gray-600 flex items-center gap-6 mb-1">
                                <span class="flex items-center">
                                    <span class="mr-1">💬</span>
                                    <span class="font-bold">{{ $restaurant->reviews->count() }}</span>件
                                </span>
                                <span class="flex items-center">
                                    <span class="mr-1">🔖</span>
                                    <span class="font-bold">{{ $restaurant->favorites->count() }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 住所・エリアの簡易表示 --}}
                    <div class="text-gray-500 mb-6 flex items-center gap-2 text-sm">
                        <span>📍 {{ $restaurant->city->prefecture->name }}{{ $restaurant->city->name }}</span>
                        @if($restaurant->nearest_station)
                            <span class="border-l border-gray-300 pl-2 ml-1">🚃 {{ $restaurant->nearest_station }}</span>
                        @endif
                    </div>

                    {{-- お気に入りボタン --}}
                    @auth
                        <div>
                            @if($restaurant->favorites()->where('user_id', Auth::id())->exists())
                                <form action="{{ route('favorites.destroy', $restaurant->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 px-6 py-2 rounded-full font-bold flex items-center gap-2 transition">
                                        ❤️ お気に入り解除
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('favorites.store', $restaurant->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-gray-100 text-gray-500 hover:text-red-500 hover:bg-red-50 border border-gray-200 px-6 py-2 rounded-full font-bold flex items-center gap-2 transition">
                                        🤍 お気に入り登録
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>


                {{-- ========================================== --}}
                {{-- ★★★ タブリスト ★★★ --}}
                {{-- ========================================== --}}
                <div class="border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500" id="myTab" role="tablist">
                        
                        {{-- トップ --}}
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 js-tab-trigger active-tab" 
                                    id="top-tab" data-target="top" type="button" role="tab">
                                トップ
                            </button>
                        </li>
                        
                        {{-- メニュー --}}
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 js-tab-trigger" 
                                    id="menu-tab" data-target="menu" type="button" role="tab">
                                メニュー
                            </button>
                        </li>

                        {{-- レビュー --}}
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 js-tab-trigger" 
                                    id="reviews-tab" data-target="reviews" type="button" role="tab">
                                レビュー <span class="bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs ml-1">{{ $restaurant->reviews->count() }}</span>
                            </button>
                        </li>

                        {{-- アクセス --}}
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 js-tab-trigger"
                                    id="access-tab" data-target="access" type="button" role="tab">
                                アクセス
                            </button>
                        </li>

                        {{-- 予約 --}}
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 js-tab-trigger"
                                    id="reservation-tab" data-target="reservation" type="button" role="tab">
                                予約
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- ========================================== --}}
                {{-- ★★★ タブコンテンツ ★★★ --}}
                {{-- ========================================== --}}
                <div id="myTabContent" class="p-6 lg:p-10 min-h-[400px]">
                    
                    {{-- 1. トップタブ --}}
                    <div class="js-tab-content block" id="top" role="tabpanel">
                        
                        {{-- 店舗画像 --}}
                        <div class="mb-8">
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
                                    <div class="absolute bottom-4 right-4 bg-black/60 text-white text-xs px-3 py-1 rounded-full pointer-events-none">
                                        {{ $restaurant->images->count() }}枚の写真
                                    </div>
                                </div>
                            @else
                                <div class="aspect-video bg-gray-50 rounded-lg flex flex-col items-center justify-center text-gray-400 border border-gray-200 border-dashed">
                                    <span class="text-4xl mb-2">📷</span>
                                    <p>画像は登録されていません</p>
                                </div>
                            @endif
                        </div>

                        {{-- お店の紹介 --}}
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-4 border-l-4 border-orange-500 pl-3">お店の紹介</h2>
                            <p class="text-gray-700 leading-loose whitespace-pre-wrap">{{ $restaurant->description }}</p>
                        </div>
                    </div>


                    {{-- 2. メニュータブ --}}
                    <div class="js-tab-content hidden" id="menu" role="tabpanel">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-l-4 border-orange-500 pl-3">メニュー・価格情報</h2>
                        @if($restaurant->menu_info)
                            <div class="bg-orange-50 border border-orange-100 p-6 rounded-lg text-gray-700 leading-relaxed whitespace-pre-wrap">
                                {{ $restaurant->menu_info }}
                            </div>
                        @else
                            <p class="text-gray-500">メニュー情報はまだ登録されていません。</p>
                        @endif
                    </div>


                    {{-- 3. レビュータブ --}}
                    <div class="js-tab-content hidden" id="reviews" role="tabpanel">
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            {{-- 左側：投稿フォーム --}}
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 sticky top-4">
                                    <h3 class="font-bold mb-4 text-gray-800">レビューを書く</h3>
                                    @auth
                                        <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-4">
                                                <label class="block text-sm font-bold mb-1 text-gray-700">評価</label>
                                                <select name="rating" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                                    <option value="5" selected>⭐️⭐️⭐️⭐️⭐️ 5</option>
                                                    <option value="4">⭐️⭐️⭐️⭐️ 4</option>
                                                    <option value="3">⭐️⭐️⭐️ 3</option>
                                                    <option value="2">⭐️⭐️ 2</option>
                                                    <option value="1">⭐️ 1</option>
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm font-bold mb-1 text-gray-700">コメント</label>
                                                <textarea name="comment" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="感想を教えてください"></textarea>
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm font-bold mb-1 text-gray-700">画像</label>
                                                <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200">
                                            </div>
                                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-full transition shadow-md">
                                                投稿する
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-sm text-gray-500 mb-4">レビューを投稿するにはログインが必要です。</p>
                                        <a href="{{ route('login') }}" class="block text-center border border-orange-500 text-orange-500 font-bold py-2 rounded-full hover:bg-orange-50">ログイン</a>
                                    @endauth
                                </div>
                            </div>

                            {{-- 右側：レビュー一覧 --}}
                            <div class="lg:col-span-2">
                                <h3 class="font-bold mb-4 text-gray-800 text-lg">新着レビュー</h3>
                                @if($restaurant->reviews->isEmpty())
                                    <div class="text-center py-10 bg-gray-50 rounded-lg text-gray-500">
                                        まだレビューはありません。<br>最初の投稿者になりましょう！
                                    </div>
                                @else
                                    <div class="space-y-6">
                                        @foreach($restaurant->reviews as $review)
                                            <div class="border-b border-gray-100 pb-6 last:border-0">
                                                <div class="flex justify-between items-center mb-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-gray-800">👤 {{ $review->user->name }}</span>
                                                        <span class="text-yellow-500 text-sm">
                                                            {{ str_repeat('★', $review->rating) }}<span class="text-gray-300">{{ str_repeat('★', 5 - $review->rating) }}</span>
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-400">{{ $review->created_at->format('Y/m/d') }}</span>
                                                </div>
                                                <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap mb-3">{{ $review->comment }}</p>
                                                
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
                                                        @if($review->images->count() > 1)
                                                            <div class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-0.5 rounded pointer-events-none">
                                                                +{{ $review->images->count() - 1 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(Auth::id() === $review->user_id)
                                                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="mt-2 text-right" onsubmit="return confirm('削除しますか？');">
                                                        @csrf @method('DELETE')
                                                        <button class="text-xs text-red-400 hover:text-red-600 hover:underline">削除する</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>


                    {{-- 4. アクセスタブ --}}
                    <div class="js-tab-content hidden" id="access" role="tabpanel">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 border-l-4 border-orange-500 pl-3">店舗へのアクセス</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-xs text-gray-500 font-bold mb-1">住所</p>
                                    <p class="text-lg">{{ $restaurant->address }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-xs text-gray-500 font-bold mb-1">最寄り駅</p>
                                    <p class="text-lg">
                                        {{ $restaurant->nearest_station ?? '情報なし' }}
                                    </p>
                                </div>

                                <div class="pt-4">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($restaurant->address) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline font-bold">
                                        <span class="mr-2">🗺️</span> Googleマップで開く
                                    </a>
                                </div>
                            </div>

                            {{-- ★★★ Googleマップ埋め込みエリア ★★★ --}}
                            <div class="h-[300px] w-full bg-gray-100 rounded-lg overflow-hidden shadow-sm border border-gray-200">
                                @php
                                    $mapQuery = urlencode($restaurant->address);
                                    if ($restaurant->latitude && $restaurant->longitude) {
                                        $mapQuery = "{$restaurant->latitude},{$restaurant->longitude}";
                                    }
                                @endphp
                                <iframe
                                    src="https://maps.google.com/maps?q={{ $mapQuery }}&output=embed&t=m&z=15"
                                    width="100%"
                                    height="100%"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy">
                                </iframe>
                            </div>
                        </div>
                    </div>


                    {{-- 5. 予約タブ --}}
                    <div class="js-tab-content hidden" id="reservation" role="tabpanel">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 border-l-4 border-orange-500 pl-3">予約</h2>

                        {{-- 成功メッセージ --}}
                        @if (session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- エラーメッセージ --}}
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($restaurant->seatTypes->isEmpty())
                            <div class="text-center py-10 bg-gray-50 rounded-lg text-gray-500">
                                この店舗は現在予約を受け付けていません。
                            </div>
                        @else
                            @auth
                                <div class="max-w-lg">
                                    <form action="{{ route('reservations.store', $restaurant->id) }}" method="POST">
                                        @csrf

                                        <div class="mb-4">
                                            <label class="block text-sm font-bold mb-1 text-gray-700">日付</label>
                                            <input type="date" name="reservation_date" value="{{ old('reservation_date') }}"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>

                                        <div class="mb-4">
                                            <label class="block text-sm font-bold mb-1 text-gray-700">時間</label>
                                            <input type="time" name="reservation_time" value="{{ old('reservation_time') }}"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>

                                        <div class="mb-4">
                                            <label class="block text-sm font-bold mb-1 text-gray-700">座席タイプ</label>
                                            <select name="seat_type_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                                <option value="">選択してください</option>
                                                @foreach ($restaurant->seatTypes as $seatType)
                                                    <option value="{{ $seatType->id }}" {{ old('seat_type_id') == $seatType->id ? 'selected' : '' }}>
                                                        {{ $seatType->name }}（定員: {{ $seatType->capacity }}）
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-6">
                                            <label class="block text-sm font-bold mb-1 text-gray-700">人数</label>
                                            <input type="number" name="number_of_people" min="1" value="{{ old('number_of_people', 1) }}"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>

                                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-full transition shadow-md">
                                            予約を確定する
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 text-center">
                                    <p class="text-gray-500 mb-4">予約するにはログインが必要です。</p>
                                    <a href="{{ route('login') }}" class="inline-block border border-orange-500 text-orange-500 font-bold py-2 px-6 rounded-full hover:bg-orange-50 transition">ログイン</a>
                                </div>
                            @endauth
                        @endif
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

    {{-- スタイルとスクリプト --}}
    <style>
        .active-tab {
            color: #f97316 !important; /* text-orange-500 */
            border-bottom-color: #f97316 !important; /* border-orange-500 */
        }
    </style>

    <script>
        // タブ切り替え
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.js-tab-trigger');
            const contents = document.querySelectorAll('.js-tab-content');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetId = tab.getAttribute('data-target');
                    tabs.forEach(t => t.classList.remove('active-tab', 'border-orange-500', 'text-orange-600'));
                    tabs.forEach(t => t.classList.add('border-transparent'));
                    contents.forEach(c => c.classList.add('hidden'));
                    contents.forEach(c => c.classList.remove('block'));
                    tab.classList.add('active-tab');
                    tab.classList.remove('border-transparent');
                    document.getElementById(targetId).classList.remove('hidden');
                    document.getElementById(targetId).classList.add('block');
                });
            });
        });

        // モーダル機能
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
</x-app-layout>