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
        Schema::table('restaurants', function (Blueprint $table) {
            // まだカラムが存在しない場合のみ追加する安全策
            if (!Schema::hasColumn('restaurants', 'nearest_station')) {
                $table->string('nearest_station')->nullable()->after('city_id');
            }
            if (!Schema::hasColumn('restaurants', 'menu_info')) {
                $table->text('menu_info')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['nearest_station', 'menu_info']);
        });
    }
};