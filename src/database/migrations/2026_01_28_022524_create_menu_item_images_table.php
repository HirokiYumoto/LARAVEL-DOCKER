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
        Schema::create('menu_item_images', function (Blueprint $table) {
            $table->id();

            // どの料理の画像？（料理が消えたら画像も消える）
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();

            $table->string('image_url'); // 画像のパス（S3やstorageの場所）
            $table->string('alt_text')->nullable(); // 画像の説明（SEO用など）

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_images');
    }
};
