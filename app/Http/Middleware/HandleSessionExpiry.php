<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HandleSessionExpiry
{
    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated and session is expired
        if (auth()->check()) {
            // Check if session was invalidated
            $sessionKey = 'last_activity';
            $sessionTimeout = config('session.lifetime') * 60; // Convert minutes to seconds

            if (Session::has($sessionKey)) {
                $timeSinceLastActivity = time() - Session::get($sessionKey);

                if ($timeSinceLastActivity > $sessionTimeout) {
                    // Session has expired
                    auth()->logout();
                    Session::invalidate();
                    Session::regenerateToken();

                    // Store expiry message for next request
                    Session::flash('session_expired', true);

                    return redirect()->route('login')->with('message', 'Your session has expired. Please log in again.');
                }
            }

            // Update last activity
            Session::put($sessionKey, time());
        }

        return $next($request);
    }
}
