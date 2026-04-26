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
        // Get locale from session, cookie, or use default
        $locale = Session::get('locale') ?? 
                  $request->cookie('locale') ?? 
                  config('app.locale');

        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale');
        }

        // Set application locale
        app()->setLocale($locale);

        // Store in session for consistency
        Session::put('locale', $locale);

        // Continue to next middleware/route
        $response = $next($request);

        // Set locale cookie (expires in 1 year)
        return $response->cookie('locale', $locale, 60 * 24 * 365);
    }
}
