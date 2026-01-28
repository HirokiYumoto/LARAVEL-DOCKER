<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // ★追加
use App\Models\Prefecture; // ★追加

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ★追加：ヘッダー（components.site-header）が表示される時は、
        // 常に $headerPrefectures という変数で都道府県データを渡す
        View::composer('components.site-header', function ($view) {
            $view->with('headerPrefectures', Prefecture::all());
        });
    }
}