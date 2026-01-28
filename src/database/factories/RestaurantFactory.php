<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // ユーザーとエリアも自動で一緒に作る設定
            'user_id' => \App\Models\User::factory(),
            'city_id' => \App\Models\City::factory(),
            
            'name' => fake()->company() . '食堂',
            'description' => fake()->realText(50),
            'address_detail' => fake()->streetAddress(),
            'phone_number' => fake()->phoneNumber(),
            'open_time' => '10:00:00',
            'close_time' => '22:00:00',
        ];
    }
}
