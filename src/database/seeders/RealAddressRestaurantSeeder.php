<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Models\City;
use App\Models\User;
use Faker\Factory as Faker;

class RealAddressRestaurantSeeder extends Seeder
{
    /**
     * 実在する住所、座標、最寄り駅を持つダミー店舗を生成
     */
    public function run(): void
    {
        // 1. 既存の店舗データを削除
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Restaurant::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ja_JP');

        // 店舗オーナー用のユーザーIDを取得
        $user = User::first() ?? User::factory()->create();

        // 実在する住所・座標・最寄り駅のリスト
        $locations = [
            [
                'city_name' => '千代田区',
                'address' => '丸の内1-9-1 グラン東京ノースタワー',
                'lat' => 35.681236, 'lng' => 139.767125,
                'station' => '東京駅'
            ],
            [
                'city_name' => '新宿区',
                'address' => '西新宿2-8-1',
                'lat' => 35.689634, 'lng' => 139.692101,
                'station' => '都庁前駅'
            ],
            [
                'city_name' => '渋谷区',
                'address' => '宇田川町15-1 渋谷パルコ',
                'lat' => 35.662058, 'lng' => 139.698375,
                'station' => '渋谷駅'
            ],
            [
                'city_name' => '札幌市',
                'address' => '中央区北5条西2丁目5 JRタワー',
                'lat' => 43.068661, 'lng' => 141.350755,
                'station' => '札幌駅'
            ],
            [
                'city_name' => '大阪市',
                'address' => '北区梅田3-1-1 グランフロント大阪',
                'lat' => 34.702485, 'lng' => 135.495951,
                'station' => '大阪駅'
            ],
            [
                'city_name' => '京都市',
                'address' => '下京区烏丸通塩小路下ル 京都駅ビル',
                'lat' => 34.985849, 'lng' => 135.758767,
                'station' => '京都駅'
            ],
            [
                'city_name' => '福岡市',
                'address' => '博多区博多駅中央街1-1 JR博多シティ',
                'lat' => 33.589728, 'lng' => 130.420727,
                'station' => '博多駅'
            ],
            [
                'city_name' => '横浜市',
                'address' => '西区みなとみらい2-2-1 ランドマークタワー',
                'lat' => 35.454980, 'lng' => 139.631267,
                'station' => 'みなとみらい駅'
            ],
            [
                'city_name' => '名古屋市',
                'address' => '中村区名駅1-1-4 JRセントラルタワーズ',
                'lat' => 35.170915, 'lng' => 136.881537,
                'station' => '名古屋駅'
            ],
            [
                'city_name' => '神戸市',
                'address' => '中央区東川崎町1-6-1 モザイク',
                'lat' => 34.678456, 'lng' => 135.184373,
                'station' => '神戸駅'
            ]
        ];

        foreach ($locations as $loc) {
            // CityIDの取得（なければ1）
            $city = City::where('name', 'like', "%{$loc['city_name']}%")->first();
            $city_id = $city ? $city->id : 1;

            Restaurant::create([
                'user_id' => $user->id,
                'city_id' => $city_id,
                'name' => $faker->lastName . '食堂 ' . $loc['city_name'] . '店',
                
                'address' => $loc['address'],
                'latitude' => $loc['lat'],
                'longitude' => $loc['lng'],
                
                // ここで設定したリアルな駅名を使用
                'nearest_station' => $loc['station'],
                
                'description' => $faker->realText(50),
                'menu_info' => $faker->realText(50),
                
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}