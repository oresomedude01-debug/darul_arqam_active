<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * PWA Authentication Service
 * Handles secure persistent login for PWA applications
 * Uses encrypted tokens stored in localStorage-like persistent cookies
 */
class PWAAuthService
{
    private const TOKEN_COOKIE_NAME = 'pwa_auth_token';
    private const REFRESH_TOKEN_COOKIE_NAME = 'pwa_refresh_token';
    private const TOKEN_EXPIRY_DAYS = 30;
    private const REFRESH_TOKEN_EXPIRY_DAYS = 60;

    /**
     * Generate secure persistent authentication token
     */
    public function generatePersistentToken($user)
    {
        // Generate access token (shorter lived)
        $accessToken = [
            'user_id' => $user->id,
            'email' => $user->email,
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addDays(self::TOKEN_EXPIRY_DAYS)->timestamp,
            'token_id' => Str::random(32),
        ];

        // Generate refresh token (longer lived, for renewing access token)
        $refreshToken = [
            'user_id' => $user->id,
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addDays(self::REFRESH_TOKEN_EXPIRY_DAYS)->timestamp,
            'token_id' => Str::random(32),
        ];

        return [
            'accessToken' => Crypt::encryptString(json_encode($accessToken)),
            'refreshToken' => Crypt::encryptString(json_encode($refreshToken)),
            'expiresIn' => self::TOKEN_EXPIRY_DAYS * 24 * 60 * 60, // In seconds
        ];
    }

    /**
     * Store tokens in secure cookies (HttpOnly, Secure, SameSite)
     */
    public function storeTokensInCookies($tokens)
    {
        // Access token cookie (shorter lifetime)
        Cookie::queue(
            Cookie::make(
                self::TOKEN_COOKIE_NAME,
                $tokens['accessToken'],
                self::TOKEN_EXPIRY_DAYS * 24 * 60, // Minutes
                path: '/',
                domain: config('session.domain'),
                secure: config('session.secure') ?? true,
                httpOnly: true,
                sameSite: 'lax'
            )
        );

        // Refresh token cookie (longer lifetime, not sent to JS)
        Cookie::queue(
            Cookie::make(
                self::REFRESH_TOKEN_COOKIE_NAME,
                $tokens['refreshToken'],
                self::REFRESH_TOKEN_EXPIRY_DAYS * 24 * 60, // Minutes
                path: '/',
                domain: config('session.domain'),
                secure: config('session.secure') ?? true,
                httpOnly: true,
                sameSite: 'strict'
            )
        );
    }

    /**
     * Verify and validate access token
     */
    public function verifyAccessToken($token)
    {
        try {
            $data = json_decode(Crypt::decryptString($token), true);

            // Check expiry
            if ($data['expires_at'] < now()->timestamp) {
                return null;
            }

            return $data;
        } catch (\Throwable $e) {
            \Log::warning('PWA Token verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify and validate refresh token
     */
    public function verifyRefreshToken($token)
    {
        try {
            $data = json_decode(Crypt::decryptString($token), true);

            // Check expiry
            if ($data['expires_at'] < now()->timestamp) {
                return null;
            }

            return $data;
        } catch (\Throwable $e) {
            \Log::warning('PWA Refresh Token verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken($refreshToken, $user)
    {
        $refreshData = $this->verifyRefreshToken($refreshToken);

        if (!$refreshData || $refreshData['user_id'] != $user->id) {
            return null;
        }

        // Generate new access token
        $newAccessToken = [
            'user_id' => $user->id,
            'email' => $user->email,
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addDays(self::TOKEN_EXPIRY_DAYS)->timestamp,
            'token_id' => Str::random(32),
        ];

        return [
            'accessToken' => Crypt::encryptString(json_encode($newAccessToken)),
            'expiresIn' => self::TOKEN_EXPIRY_DAYS * 24 * 60 * 60,
        ];
    }

    /**
     * Revoke tokens (logout)
     */
    public function revokeTokens()
    {
        Cookie::queue(Cookie::forget(self::TOKEN_COOKIE_NAME));
        Cookie::queue(Cookie::forget(self::REFRESH_TOKEN_COOKIE_NAME));
    }

    /**
     * Get token cookie name
     */
    public static function getTokenCookieName()
    {
        return self::TOKEN_COOKIE_NAME;
    }

    /**
     * Get refresh token cookie name
     */
    public static function getRefreshTokenCookieName()
    {
        return self::REFRESH_TOKEN_COOKIE_NAME;
    }
}
