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
        // カラムがまだ無い場合のみ追加処理を行う（これでエラーを回避できます）
        if (!Schema::hasColumn('restaurants', 'latitude')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->double('latitude', 10, 8)->nullable()->after('address')->comment('緯度');
                $table->double('longitude', 11, 8)->nullable()->after('latitude')->comment('経度');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};