<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // テーブル名は単数形同士をアルファベット順（genre_restaurant）にするのが
        // Laravelの慣習ですが、今回はわかりやすく restaurant_genre にしています。
        Schema::create('restaurant_genre', function (Blueprint $table) {
            $table->id();

            // どの店が？
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            // どのジャンル？
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();

            $table->timestamps();

            // 「A店は焼肉」というデータが2回登録されないようにする制限
            $table->unique(['restaurant_id', 'genre_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_genre');
    }
};
