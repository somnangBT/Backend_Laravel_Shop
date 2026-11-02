<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

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
        // បង្ខំឱ្យប្រើ HTTPS ជានិច្ចនៅពេលរត់លើ Render (Production)
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        Blade::component('components.card', 'card');

        // Share admin settings across admin layout and settings views (Cached to prevent slow DB roundtrips)
        View::composer(['layouts.app', 'settings.*'], function ($view) {
            try {
                $adminSettings = \Illuminate\Support\Facades\Cache::remember('admin_settings', 3600, function () {
                    return Setting::first();
                });
            } catch (\Throwable $e) {
                $adminSettings = null;
            }
            $view->with('adminSettings', $adminSettings);
        });
    }
}
