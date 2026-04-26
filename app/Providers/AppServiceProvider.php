<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\AuthorizationHelper;
use App\Helpers\LocalizationHelper;
use Blade;

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

        // Register localization Blade directives
        $this->registerLocalizationDirectives();
    }

    /**
     * Register localization Blade directives
     */
    private function registerLocalizationDirectives(): void
    {
        // @isArabic / @endIsArabic
        Blade::if('isArabic', function () {
            return LocalizationHelper::isArabic();
        });

        // @isEnglish / @endIsEnglish
        Blade::if('isEnglish', function () {
            return LocalizationHelper::isEnglish();
        });

        // @isRtl / @endIsRtl
        Blade::if('isRtl', function () {
            return LocalizationHelper::isRtl();
        });

        // @isLtr / @endIsLtr
        Blade::if('isLtr', function () {
            return LocalizationHelper::getDirection() === 'ltr';
        });
    }
}

