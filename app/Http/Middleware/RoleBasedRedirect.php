<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class RoleBasedRedirect
{
    /**
     * Handle an incoming request and redirect to appropriate dashboard based on user role
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is trying to access the generic dashboard
        if ($request->route()->getName() === 'dashboard') {
            
            // Students go to student portal
            if ($user->hasRole('student')) {
                return redirect()->route('student-portal.dashboard');
            }

            // Parents go to parent portal
            if ($user->hasRole('parent')) {
                return redirect()->route('parent-portal.dashboard');
            }

            // Admin, Teacher, and other roles access the main dashboard
            // No redirect needed, continue to main dashboard
        }

        return $next($request);
    }
}
