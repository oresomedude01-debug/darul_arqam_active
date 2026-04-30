<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PWAAuthService;
use Illuminate\Http\Request;

class PWAAuthController extends Controller
{
    private PWAAuthService $authService;

    public function __construct(PWAAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Generate PWA persistent login tokens
     * POST /api/pwa/auth/login
     */
    public function login(Request $request)
    {
        // Validate user credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt login
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth()->user();

        // Generate tokens
        $tokens = $this->authService->generatePersistentToken($user);

        // Store tokens in secure cookies
        $this->authService->storeTokensInCookies($tokens);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'expiresIn' => $tokens['expiresIn']
        ]);
    }

    /**
     * Refresh access token using refresh token
     * POST /api/pwa/auth/refresh
     */
    public function refresh(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get refresh token from cookie
        $refreshToken = $request->cookie(PWAAuthService::getRefreshTokenCookieName());

        if (!$refreshToken) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token not found'
            ], 401);
        }

        // Refresh the access token
        $newTokens = $this->authService->refreshAccessToken($refreshToken, $user);

        if (!$newTokens) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed'
            ], 401);
        }

        // Store new access token
        \Cookie::queue(
            \Cookie::make(
                PWAAuthService::getTokenCookieName(),
                $newTokens['accessToken'],
                30 * 24 * 60, // 30 days
                path: '/',
                domain: config('session.domain'),
                secure: config('session.secure') ?? true,
                httpOnly: true,
                sameSite: 'lax'
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed',
            'expiresIn' => $newTokens['expiresIn']
        ]);
    }

    /**
     * Logout - Revoke tokens
     * POST /api/pwa/auth/logout
     */
    public function logout(Request $request)
    {
        // Revoke tokens
        $this->authService->revokeTokens();

        // Laravel logout
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get current authenticated user
     * GET /api/pwa/auth/me
     */
    public function getAuthenticatedUser(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }

    /**
     * Cache invalidation endpoint (for admin)
     * POST /api/pwa/cache/invalidate
     */
    public function invalidateCache(Request $request)
    {
        // Only admins can invalidate cache
        if (!auth()->check() || !auth()->user()->hasRole('Administrator')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cacheType = $request->get('type', 'all'); // 'blog', 'all'

        \Cache::flush(); // Flush application cache

        return response()->json([
            'success' => true,
            'message' => ucfirst($cacheType) . ' cache invalidated',
            'invalidatedAt' => now()->toIso8601String()
        ]);
    }
}
