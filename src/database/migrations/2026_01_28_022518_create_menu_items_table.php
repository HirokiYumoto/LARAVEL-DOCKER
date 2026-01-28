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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            // どの店のメニュー？（店が消えたらメニューも消える）
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // 料理名
            $table->integer('price'); // 価格
            $table->text('description')->nullable(); // 料理の説明

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
