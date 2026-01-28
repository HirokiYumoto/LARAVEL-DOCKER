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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();

            // 外部キー（紐付け）
            // ユーザー(オーナー)が消えたら、その人の店も消える設定
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 市区町村が消えたら、そのエリアの店も消える設定
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();

            // 店舗の基本情報
            $table->string('name'); // 店名
            $table->text('description')->nullable(); // 説明文（長文OK・空でもOK）
            $table->string('address_detail')->nullable(); // 番地・ビル名
            $table->string('phone_number')->nullable(); // 電話番号
            
            // 営業時間（時間データのみ）
            $table->time('open_time')->nullable(); 
            $table->time('close_time')->nullable();

            $table->timestamps(); // 作成日・更新日
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};