<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\AuthorizationHelper;

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
        // Set locale from session
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        // Register authorization Blade directives
        AuthorizationHelper::registerDirectives();
    }
}

