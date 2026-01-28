<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ▼▼▼ 注意：ここの記述を確認してください ▼▼▼
        
        // もし以下のような Factory の記述があったら、必ず削除またはコメントアウトしてください。
        // これらが残っていると、勝手にアルファベットの出鱈目なデータが作られてしまいます。
        // \App\Models\User::factory(10)->create();
        // \App\Models\Restaurant::factory(10)->create(); 
        // \App\Models\City::factory(10)->create();

        // ▼▼▼ 正しい記述：作成したSeederだけを順番に呼び出します ▼▼▼
        $this->call([
            // 1. まず県データを作る
            PrefectureSeeder::class,
            // 2. 次に市町村データを作る（県データが必要だから2番目）
            CitySeeder::class,
            // 3. 最後に店舗データを作る（市データが必要だから3番目）
            RestaurantSeeder::class,
            
            // ユーザーなどのデータも必要であればここに追加
            // UserSeeder::class, 
        ]);
    }
}