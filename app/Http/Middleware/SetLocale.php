<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session or use default
        $locale = Session::get('locale', config('app.locale', 'en'));

        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale', 'en');
        }

        // Set application locale
        app()->setLocale($locale);

        return $next($request);
    }
}

