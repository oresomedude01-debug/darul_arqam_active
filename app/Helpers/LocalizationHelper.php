<?php

namespace App\Helpers;

class LocalizationHelper
{
    /**
     * Check if the current locale is Arabic
     */
    public static function isArabic(): bool
    {
        return app()->getLocale() === 'ar';
    }

    /**
     * Check if the current locale is English
     */
    public static function isEnglish(): bool
    {
        return app()->getLocale() === 'en';
    }

    /**
     * Get the current locale direction (ltr or rtl)
     */
    public static function getDirection(): string
    {
        return self::isArabic() ? 'rtl' : 'ltr';
    }

    /**
     * Get the current locale
     */
    public static function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    /**
     * Get all available locales
     */
    public static function getAvailableLocales(): array
    {
        return ['en', 'ar'];
    }

    /**
     * Get locale display name
     */
    public static function getLocaleDisplayName(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        $names = [
            'en' => 'English',
            'ar' => 'العربية',
        ];

        return $names[$locale] ?? $locale;
    }

    /**
     * Switch to a different locale
     */
    public static function switchLocale(string $locale): void
    {
        if (in_array($locale, self::getAvailableLocales())) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
            cookie('locale', $locale, 60 * 24 * 365);
        }
    }

    /**
     * Get locale URL
     */
    public static function getLocaleUrl(string $locale, string $currentUrl = null): string
    {
        return route('locale.switch', ['locale' => $locale]);
    }

    /**
     * Check if RTL
     */
    public static function isRtl(): bool
    {
        return self::isArabic();
    }

    /**
     * Get margin/padding direction helper
     * Returns 'start' or 'end' based on locale
     */
    public static function getDirectionClass(string $prefix = '', string $value = ''): string
    {
        if (self::isArabic()) {
            return $prefix ? "{$prefix}-r{$value}" : "r{$value}";
        }
        return $prefix ? "{$prefix}-l{$value}" : "l{$value}";
    }

    /**
     * Get text alignment class
     */
    public static function getTextAlignmentClass(string $direction = 'start'): string
    {
        if ($direction === 'start') {
            return self::isArabic() ? 'text-right' : 'text-left';
        } elseif ($direction === 'end') {
            return self::isArabic() ? 'text-left' : 'text-right';
        }
        return $direction;
    }
}
