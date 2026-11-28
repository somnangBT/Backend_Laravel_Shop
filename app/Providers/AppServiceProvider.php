<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
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

        // Ensure products.deleted_at exists for SoftDeletes even before manual CLI migration
        if (!$this->app->runningInConsole()) {
            try {
                $schemaCacheKey = 'schema_products_has_deleted_at_v1';
                if (!Cache::get($schemaCacheKey)) {
                    if (Schema::hasTable('products')) {
                        if (!Schema::hasColumn('products', 'deleted_at')) {
                            Schema::table('products', function ($table) {
                                $table->softDeletes();
                            });
                        }
                        if (Schema::hasColumn('products', 'deleted_at')) {
                            Cache::forever($schemaCacheKey, true);
                        }
                    }
                }

                $ordersTgCacheKey = 'schema_orders_has_telegram_msg_id_v1';
                if (!Cache::get($ordersTgCacheKey)) {
                    if (Schema::hasTable('orders')) {
                        if (!Schema::hasColumn('orders', 'telegram_message_id')) {
                            Schema::table('orders', function ($table) {
                                $table->unsignedBigInteger('telegram_message_id')->nullable();
                            });
                        }
                        if (Schema::hasColumn('orders', 'telegram_message_id')) {
                            Cache::forever($ordersTgCacheKey, true);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Skip silently if DB is not ready yet
            }
        }

        // Share admin settings across admin layout and settings views (Cached 5m to cut DB roundtrips)
        View::composer(['layouts.app', 'settings.*'], function ($view) {
            try {
                $adminSettings = Cache::remember('admin_settings', 300, function () {
                    return Setting::first();
                });
            } catch (\Throwable $e) {
                $adminSettings = null;
            }
            $view->with('adminSettings', $adminSettings);
        });
    }
}
