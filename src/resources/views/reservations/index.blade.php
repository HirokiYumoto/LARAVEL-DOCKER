<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約履歴</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">
                マイページ（予約履歴）
            </h1>
            <a href="/" class="text-blue-600 hover:text-blue-900">トップへ戻る</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-xl font-bold text-gray-700 mb-4">あなたの予約一覧</h2>

        @if($reservations->isEmpty())
            <div class="bg-white p-8 rounded shadow text-center text-gray-500">
                <p class="mb-4">現在、予約はありません。</p>
                <a href="/" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    お店を探す
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($reservations as $reservation)
                    <div class="bg-white p-6 rounded shadow flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        
                        {{-- 予約情報 --}}
                        <div class="mb-4 sm:mb-0">
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $reservation->restaurant->name }}
                            </h3>
                            <div class="text-gray-600 mt-1">
                                <span class="font-bold">{{ $reservation->reserved_at->format('Y年m月d日 H:i') }}</span>
                                <span class="mx-2">|</span>
                                {{ $reservation->number_of_people }}名
                                <span class="mx-2">|</span>
                                {{ $reservation->seatType->name ?? '席指定なし' }}
                            </div>
                            
                            {{-- 過去の予約かどうか判定 --}}
                            @if($reservation->reserved_at->isPast())
                                <span class="inline-block mt-2 bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded">来店済み</span>
                            @else
                                <span class="inline-block mt-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded">来店待ち</span>
                            @endif
                        </div>

                        {{-- キャンセルボタン（未来の予約のみ表示） --}}
                        @if(!$reservation->reserved_at->isPast())
                            <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" 
                                  onsubmit="return confirm('本当にキャンセルしますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded hover:bg-red-100 transition text-sm">
                                    予約キャンセル
                                </button>
                            </form>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>