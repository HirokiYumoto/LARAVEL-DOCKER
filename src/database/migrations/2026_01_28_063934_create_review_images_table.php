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
        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            // どのレビューの画像か？
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            // 画像の保存場所（パス）
            $table->string('image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
