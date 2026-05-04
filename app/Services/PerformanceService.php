<?php

namespace App\Services;

/**
 * Performance Optimization Service
 * Provides caching and query optimization utilities
 */
class PerformanceService
{
    /**
     * Cache duration for different data types (seconds)
     */
    const CACHE_DURATIONS = [
        'students' => 3600,        // 1 hour
        'teachers' => 3600,        // 1 hour
        'classes' => 7200,         // 2 hours
        'subjects' => 86400,       // 24 hours (rarely changes)
        'sessions' => 86400,       // 24 hours
        'terms' => 86400,          // 24 hours
        'dashboard' => 300,        // 5 minutes (frequent updates)
        'bills' => 1800,           // 30 minutes
        'results' => 3600,         // 1 hour
        'timetable' => 86400,      // 24 hours
        'attendance' => 300,       // 5 minutes
        'roles_permissions' => 86400, // 24 hours
    ];

    /**
     * Get cache key for specific data type
     */
    public static function getCacheKey($type, $identifier = null): string
    {
        $base = "portal:{$type}";
        return $identifier ? "{$base}:{$identifier}" : $base;
    }

    /**
     * Get cache duration in seconds
     */
    public static function getCacheDuration($type): int
    {
        return self::CACHE_DURATIONS[$type] ?? 300; // Default 5 minutes
    }

    /**
     * Clear all portal caches
     */
    public static function clearAllCaches(): bool
    {
        try {
            $types = array_keys(self::CACHE_DURATIONS);
            foreach ($types as $type) {
                \Cache::forget(self::getCacheKey($type));
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Cache clear failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear specific cache type
     */
    public static function clearCache($type, $identifier = null): bool
    {
        try {
            $key = self::getCacheKey($type, $identifier);
            \Cache::forget($key);
            return true;
        } catch (\Exception $e) {
            \Log::error('Cache clear failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Measure query performance
     */
    public static function debugQuery($query, $description = 'Query')
    {
        if (config('app.debug')) {
            $start = microtime(true);
            $result = $query;
            $duration = (microtime(true) - $start) * 1000;
            
            if ($duration > 100) { // Log if over 100ms
                \Log::warning("{$description} took {$duration}ms");
            }
            
            return $result;
        }
        
        return $query;
    }
}
