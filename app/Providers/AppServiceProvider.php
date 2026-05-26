<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        Model::unguard();
        View::composer('*', function ($view) {
            $settings = collect();
            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::pluck('value', 'key');
                }
            } catch (\Throwable) {
                $settings = collect();
            }
            $view->with('appSettings', $settings);
        });
    }
}
