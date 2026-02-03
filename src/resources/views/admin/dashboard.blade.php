<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            🛠️ 管理者ダッシュボード
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- メッセージ表示 --}}
            @if(session('success'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- 1. ユーザー管理エリア --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2 flex items-center gap-2">
                        👤 ユーザー管理 <span class="text-sm font-normal text-gray-500">({{ $users->count() }}名)</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm whitespace-nowrap">
                            <thead class="uppercase tracking-wider border-b-2 border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">名前</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">権限</th>
                                    <th class="px-4 py-3 text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $user->id }}</td>
                                        <td class="px-4 py-3 font-bold">{{ $user->name }}</td>
                                        <td class="px-4 py-3">{{ $user->email }}</td>
                                        <td class="px-4 py-3">
                                            @if($user->role_id == 3) <span class="text-red-600 font-bold">管理者</span>
                                            @elseif($user->role_id == 2) <span class="text-blue-600 font-bold">店舗代表</span>
                                            @else 一般 @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($user->id !== Auth::id())
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('本当にこのユーザーを削除しますか？\n関連するデータも削除される可能性があります。');">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-500 hover:text-red-700 font-bold border border-red-200 px-3 py-1 rounded hover:bg-red-50">削除</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs">自分</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 2. 店舗管理エリア --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2 flex items-center gap-2">
                        🍜 店舗管理 <span class="text-sm font-normal text-gray-500">({{ $restaurants->count() }}件)</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm whitespace-nowrap">
                            <thead class="uppercase tracking-wider border-b-2 border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">店舗名</th>
                                    <th class="px-4 py-3">オーナー</th>
                                    <th class="px-4 py-3">住所</th>
                                    <th class="px-4 py-3 text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($restaurants as $restaurant)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $restaurant->id }}</td>
                                        <td class="px-4 py-3 font-bold">
                                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-blue-600 hover:underline" target="_blank">
                                                {{ $restaurant->name }} ↗
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">{{ $restaurant->user->name ?? '不明' }}</td>
                                        <td class="px-4 py-3 text-gray-500 truncate max-w-xs">{{ $restaurant->address }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <form action="{{ route('admin.restaurants.destroy', $restaurant->id) }}" method="POST" onsubmit="return confirm('本当にこの店舗を削除しますか？');">
                                                @csrf @method('DELETE')
                                                <button class="text-red-500 hover:text-red-700 font-bold border border-red-200 px-3 py-1 rounded hover:bg-red-50">削除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 3. レビュー管理エリア --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2 flex items-center gap-2">
                        💬 レビュー管理 <span class="text-sm font-normal text-gray-500">({{ $reviews->count() }}件)</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm whitespace-nowrap">
                            <thead class="uppercase tracking-wider border-b-2 border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">投稿者</th>
                                    <th class="px-4 py-3">店舗名</th>
                                    <th class="px-4 py-3">評価</th>
                                    <th class="px-4 py-3">コメント</th>
                                    <th class="px-4 py-3 text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $review->created_at->format('Y/m/d') }}</td>
                                        <td class="px-4 py-3">{{ $review->user->name ?? '退会済み' }}</td>
                                        <td class="px-4 py-3 text-blue-600">{{ $review->restaurant->name ?? '削除済み' }}</td>
                                        <td class="px-4 py-3 text-yellow-500 font-bold">★{{ $review->rating }}</td>
                                        <td class="px-4 py-3 text-gray-600 truncate max-w-xs" title="{{ $review->comment }}">{{ $review->comment }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('このレビューを強制削除しますか？');">
                                                @csrf @method('DELETE')
                                                <button class="text-red-500 hover:text-red-700 font-bold border border-red-200 px-3 py-1 rounded hover:bg-red-50">削除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>