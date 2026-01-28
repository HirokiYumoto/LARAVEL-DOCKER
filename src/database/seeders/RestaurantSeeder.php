<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\City;
use App\Models\User; // ★追加
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash; // ★追加（パスワード用）

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Restaurant::truncate();
        Schema::enableForeignKeyConstraints();

        if (City::count() === 0) {
            $this->call(CitySeeder::class);
        }

        // ★追加：これらの店舗データの「所有者」となる管理者ユーザーを作成
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'], // このメールアドレスがあれば取得、なければ作成
            [
                'name' => '管理者太郎',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // サンプル店舗データ
        $restaurants = [
            [
                'name' => '麺屋 太郎',
                'description' => '濃厚な豚骨スープと自家製麺が自慢の一杯。ランチタイムは行列必至です。',
                'genre' => '豚骨', 
            ],
            [
                'name' => '中華そば 青葉',
                'description' => '昔ながらの醤油ラーメン。鶏ガラベースの透き通ったスープに心が安らぎます。',
                'genre' => '醤油',
            ],
            [
                'name' => '味噌乃家',
                'description' => '北海道産の味噌をふんだんに使用した、コクのある味噌ラーメン専門店。',
                'genre' => '味噌',
            ],
            [
                'name' => '海鮮ラーメン 潮騒',
                'description' => '新鮮な魚介をたっぷりトッピング。スープまで飲み干せる塩ラーメンです。',
                'genre' => '塩',
            ],
            [
                'name' => '激辛タンメン ファイヤー',
                'description' => '辛いもの好きにはたまらない！特製ラー油と唐辛子が効いた刺激的な一杯。',
                'genre' => '激辛',
            ],
            [
                'name' => 'つけ麺 大王',
                'description' => '極太麺を濃厚な魚介豚骨スープにくぐらせて。並盛・中盛・大盛が同料金！',
                'genre' => 'つけ麺',
            ],
            [
                'name' => '鶏白湯 ほっこり',
                'description' => '鶏の旨味を凝縮したクリーミーなスープ。女性にも大人気のヘルシーラーメン。',
                'genre' => '鶏白湯',
            ],
            [
                'name' => '油そば ぶぶか',
                'description' => 'スープのないラーメン「油そば」。特製タレと麺を豪快に混ぜてお召し上がりください。',
                'genre' => '油そば',
            ],
        ];

        foreach ($restaurants as $data) {
            $city = City::inRandomOrder()->first();

            Restaurant::create([
                'user_id' => $user->id, // ★追加：ここがエラーの原因でした
                'name' => $data['name'],
                'city_id' => $city->id,
                'address_detail' => '駅前 1-2-3',
                'description' => $data['description'],
                'open_time' => '11:00',
                'close_time' => '22:00',
            ]);
        }
    }
}