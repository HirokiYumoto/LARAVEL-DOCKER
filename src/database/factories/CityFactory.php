<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // ランダムに選んだ都道府県のIDを入れる
            'prefecture_id' => \App\Models\Prefecture::inRandomOrder()->first()->id,
            // 架空の市名を生成
            'name' => fake()->city(),
            'code' => fake()->randomNumber(5, true),
        ];
    }
}
