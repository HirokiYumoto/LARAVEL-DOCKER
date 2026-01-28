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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            // 都道府県との紐付け
            $table->foreignId('prefecture_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // 市区町村名
            $table->string('code')->nullable(); // 自治体コードなど
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
