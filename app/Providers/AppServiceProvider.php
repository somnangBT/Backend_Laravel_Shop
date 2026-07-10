<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

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
    }
}