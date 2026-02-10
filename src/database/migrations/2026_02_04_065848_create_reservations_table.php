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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            // 誰が、どの店の、どの席タイプを
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_seat_type_id')->constrained()->cascadeOnDelete();
            
            // 予約日時
            $table->dateTime('reserved_at');
            // 終了日時（重複チェックを高速化するために保存）
            $table->dateTime('end_at');
            
            // 人数
            $table->integer('number_of_people');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
