<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'イタリアン', 'フレンチ', '和食', '寿司', '焼肉',
            'ラーメン', '居酒屋', 'カフェ', 'スイーツ', '中華料理',
            '韓国料理', 'カレー', 'ハンバーガー', 'バー'
        ];

        foreach ($genres as $name) {
            Genre::create(['name' => $name]);
        }
    }
}