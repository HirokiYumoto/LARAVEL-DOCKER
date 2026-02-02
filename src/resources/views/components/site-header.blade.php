<header class="bg-white shadow-sm sticky top-0 z-50 h-16">
    <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between gap-2 sm:gap-4">
        
        <div class="flex-shrink-0">
            <a href="{{ route('home') }}" class="font-bold text-2xl text-orange-500 tracking-tighter flex items-center gap-2">
                <span class="text-3xl">🍜</span>
                <span class="hidden lg:inline">tabelogg</span>
            </a>
        </div>

        <div class="flex flex-grow max-w-2xl mx-2 sm:mx-4">
            <form action="{{ route('restaurants.index') }}" method="GET" class="w-full flex rounded-md shadow-sm border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500 bg-gray-50">
                
                <div class="relative w-24 sm:w-32 flex-shrink-0 border-r border-gray-200">
                    <select name="prefecture_id" class="w-full h-full py-2 pl-2 sm:pl-3 pr-6 sm:pr-8 text-xs sm:text-sm bg-transparent border-none focus:ring-0 text-gray-700 cursor-pointer truncate">
                        <option value="">エリア</option>
                        @if(isset($headerPrefectures))
                            @foreach($headerPrefectures as $pref)
                                <option value="{{ $pref->id }}" {{ request('prefecture_id') == $pref->id ? 'selected' : '' }}>
                                    {{ $pref->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="店名など" class="flex-grow py-2 px-2 sm:px-4 text-xs sm:text-sm border-none focus:ring-0 text-gray-700 placeholder-gray-400 min-w-0">

                <button type="submit" class="bg-orange-100 hover:bg-orange-200 text-orange-600 px-3 sm:px-4 flex items-center justify-center transition border-l border-gray-200 flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <div class="flex-shrink-0 flex items-center space-x-2 sm:space-x-4">
            @auth
                <div class="flex items-center gap-3">
                    
                    {{-- ユーザー名（PCのみ表示） --}}
                    <span class="text-sm font-bold text-gray-700 hidden lg:inline truncate max-w-[100px]">{{ Auth::user()->name }}さん</span>

                    {{-- ★ここを変更：アイコン付き「マイページ」ボタン --}}
                    {{-- flex items-center gap-1 でアイコンと文字を横並びに --}}
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 bg-gray-100 hover:bg-orange-100 text-gray-700 hover:text-orange-600 px-3 py-1.5 rounded-full transition duration-300 shadow-sm border border-gray-200 hover:border-orange-200">
                        {{-- 左側のアイコン --}}
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{-- 右側の文字 --}}
                        <span class="text-xs sm:text-sm font-bold whitespace-nowrap">マイページ</span>
                    </a>

                    {{-- ログアウトボタン --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs sm:text-sm text-gray-400 hover:text-red-500 font-bold transition whitespace-nowrap pt-1">
                            ログアウト
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-xs sm:text-sm text-gray-600 hover:text-orange-500 font-bold transition whitespace-nowrap">ログイン</a>
                <a href="{{ route('register') }}" class="text-xs sm:text-sm bg-orange-500 hover:bg-orange-600 text-white font-bold py-1.5 px-3 sm:py-2 sm:px-4 rounded-full transition shadow-md whitespace-nowrap">登録</a>
            @endauth
        </div>
    </div>
</header>