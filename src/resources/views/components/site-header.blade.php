<header class="bg-white shadow-sm sticky top-0 z-50 h-16">
    <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between gap-2 sm:gap-4">
        
        <div class="flex-shrink-0">
            <a href="{{ route('home') }}" class="font-bold text-2xl text-orange-500 tracking-tighter flex items-center gap-2">
                <span class="text-3xl">🍜</span>
                {{-- スマホではアイコンのみ、PCでは文字も表示 --}}
                <span class="hidden lg:inline">tabelogg</span>
            </a>
        </div>

        <div class="flex flex-grow max-w-2xl mx-2 sm:mx-4">
            <form action="{{ route('restaurants.index') }}" method="GET" class="w-full flex rounded-md shadow-sm border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500 bg-gray-50">
                
                <div class="relative w-24 sm:w-32 flex-shrink-0 border-r border-gray-200">
                    <select name="prefecture_id" class="w-full h-full py-2 pl-2 sm:pl-3 pr-6 sm:pr-8 text-xs sm:text-sm bg-transparent border-none focus:ring-0 text-gray-700 cursor-pointer truncate">
                        <option value="">エリア</option>
                        {{-- AppServiceProviderで定義した $headerPrefectures が存在する場合のみ表示 --}}
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

        <div class="flex-shrink-0 flex items-center space-x-2 sm:space-x-3">
            @auth
                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="text-xs sm:text-sm font-bold text-gray-700 hidden sm:inline">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs sm:text-sm text-gray-500 hover:text-orange-500 font-bold transition whitespace-nowrap">
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