<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Prefecture;
use Illuminate\Support\Facades\Schema;
use Normalizer; // ★追加：正規化用クラス

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        City::truncate();
        Schema::enableForeignKeyConstraints();

        $data = [
            '北海道' => '札幌市',
            '青森県' => '青森市',
            '岩手県' => '盛岡市',
            '宮城県' => '仙台市',
            '秋田県' => '秋田市',
            '山形県' => '山形市',
            '福島県' => '福島市',
            '茨城県' => '水戸市',
            '栃木県' => '宇都宮市',
            '群馬県' => '前橋市',
            '埼玉県' => 'さいたま市',
            '千葉県' => '千葉市',
            '東京都' => [
                '千代田区', '中央区', '港区', '新宿区', '文京区', '台東区', '墨田区',
                '江東区', '品川区', '目黒区', '大田区', '世田谷区', '渋谷区', '中野区',
                '杉並区', '豊島区', '北区', '荒川区', '板橋区', '練馬区', '足立区',
                '葛飾区', '江戸川区'
            ],
            '神奈川県' => '横浜市',
            '新潟県' => '新潟市',
            '富山県' => '富山市',
            '石川県' => '金沢市',
            '福井県' => '福井市',
            '山梨県' => '甲府市',
            '長野県' => '長野市',
            '岐阜県' => '岐阜市',
            '静岡県' => '静岡市',
            '愛知県' => '名古屋市',
            '三重県' => '津市',
            '滋賀県' => '大津市',
            '京都府' => '京都市',
            '大阪府' => '大阪市',
            '兵庫県' => '神戸市',
            '奈良県' => '奈良市',
            '和歌山県' => '和歌山市',
            '鳥取県' => '鳥取市',
            '島根県' => '松江市',
            '岡山県' => '岡山市',
            '広島県' => '広島市',
            '山口県' => '山口市',
            '徳島県' => '徳島市',
            '香川県' => '高松市',
            '愛媛県' => '松山市',
            '高知県' => '高知市',
            '福岡県' => '福岡市',
            '佐賀県' => '佐賀市',
            '長崎県' => '長崎市',
            '熊本県' => '熊本市',
            '大分県' => '大分市',
            '宮崎県' => '宮崎市',
            '鹿児島県' => '鹿児島市',
            '沖縄県' => '那覇市',
        ];

        foreach ($data as $prefName => $cities) {
            $prefecture = Prefecture::where('name', $prefName)->first();

            if (!$prefecture) {
                continue;
            }

            // 配列か文字列かを統一して処理しやすくする
            $cityList = is_array($cities) ? $cities : [$cities];

            foreach ($cityList as $cityName) {
                // ★正規化処理（NFKC形式）
                // 半角カナ→全角、全角英数→半角、合成文字の分解・統合などを行う
                if (class_exists('Normalizer')) {
                    $normalizedCityName = Normalizer::normalize($cityName, Normalizer::FORM_KC);
                } else {
                    $normalizedCityName = $cityName; // 拡張モジュールがない場合のフォールバック
                }

                City::create([
                    'prefecture_id' => $prefecture->id,
                    'name' => $normalizedCityName,
                ]);
            }
        }
    }
}