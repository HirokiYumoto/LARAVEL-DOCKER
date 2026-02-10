<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // latitudeカラムが無い場合のみ追加
            if (!Schema::hasColumn('restaurants', 'latitude')) {
                $table->double('latitude', 10, 7)->nullable()->after('address');
            }
            
            // longitudeカラムが無い場合のみ追加
            if (!Schema::hasColumn('restaurants', 'longitude')) {
                $table->double('longitude', 10, 7)->nullable()->after('latitude'); // latitudeの後に配置したいが、既存の場合は順序が守られない可能性あり
            }
        });
    }

    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // 削除処理も一応安全策として記述（通常はそのままでOKですが）
            if (Schema::hasColumn('restaurants', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('restaurants', 'longitude')) {
                $table->dropColumn('longitude');
            }
        });
    }
};