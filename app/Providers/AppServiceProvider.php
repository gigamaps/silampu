<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Otomatis bagikan data setting ke seluruh file Blade jika tabelnya ada
        if (Schema::hasTable('settings')) {
            $globalSettings = Setting::all()->pluck('value', 'key')->toArray();
            View::share('globalSettings', $globalSettings);
        }
    }
}
