<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\City;
use App\Models\Prefecture;

class GeoTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. テスト用オーナーの作成
        $user = User::factory()->create([
            'name' => 'テストオーナー',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            // role_idなどが必要な場合は適宜追加してください
        ]);

        // 2. テストデータの定義（都道府県・市区町村・番地を分離）
        $restaurantsData = [
            [
                'name' => '【テスト】東京駅ラーメンストリート',
                'description' => '東京駅のド真ん中。千代田区。',
                'pref' => '東京都',
                'city' => '千代田区',
                'address' => '丸の内1-9-1', // 番地のみ
                'latitude' => 35.681236,
                'longitude' => 139.767125,
            ],
            [
                'name' => '【テスト】新宿カリーハウス',
                'description' => '新宿駅近く。新宿区。',
                'pref' => '東京都',
                'city' => '新宿区',
                'address' => '新宿3-38-1',
                'latitude' => 35.689634,
                'longitude' => 139.700567,
            ],
            [
                'name' => '【テスト】横浜中華飯店',
                'description' => '横浜駅前。横浜市西区。',
                'pref' => '神奈川県',
                'city' => '横浜市西区', // 政令指定都市の区
                'address' => '高島2-16-1',
                'latitude' => 35.465786,
                'longitude' => 139.622313,
            ],
            [
                'name' => '【テスト】大阪たこ焼き本舗',
                'description' => '大阪駅。大阪市北区。',
                'pref' => '大阪府',
                'city' => '大阪市北区',
                'address' => '梅田3-1-1',
                'latitude' => 34.702485,
                'longitude' => 135.495951,
            ],
            [
                'name' => '【テスト】札幌ジンギスカン',
                'description' => '札幌駅。札幌市北区。',
                'pref' => '北海道',
                'city' => '札幌市北区',
                'address' => '北6条西4丁目',
                'latitude' => 43.068661,
                'longitude' => 141.350755,
            ],
        ];

        // 3. データ投入処理
        foreach ($restaurantsData as $data) {
            
            // ① 都道府県を取得または作成
            $pref = Prefecture::firstOrCreate(['name' => $data['pref']]);

            // ② 市区町村を取得または作成（都道府県IDと紐付ける）
            $city = City::firstOrCreate(
                ['name' => $data['city'], 'prefecture_id' => $pref->id]
            );

            // ③ 店舗を作成
            Restaurant::create([
                'user_id' => $user->id,
                'city_id' => $city->id, // 作成したCityのID
                'name' => $data['name'],
                'description' => $data['description'],
                'address' => $data['address'], // 番地のみ保存
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);
        }
    }
}