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
        Schema::create('restaurant_time_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            // 曜日 (0:日曜, 1:月曜... 7:祝日)
            $table->integer('day_of_week');
            // 時間帯（11:00〜14:00など）
            $table->time('start_time');
            $table->time('end_time');
            // この時間帯の滞在可能時間（分単位）
            $table->integer('stay_minutes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_time_settings');
    }
};
