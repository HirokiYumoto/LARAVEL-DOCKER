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
        Schema::create('restaurant_seat_types', function (Blueprint $table) {
            $table->id();
            // どのお店か
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            // 席の名前（例: カウンター席、4名テーブル）
            $table->string('name');
            // そのタイプの席数・卓数
            $table->integer('capacity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_seat_types');
    }
};
