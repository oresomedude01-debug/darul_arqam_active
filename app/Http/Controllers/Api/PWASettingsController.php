<?php

namespace App\Http\Controllers\Api;

use App\Models\SchoolSetting;
use Illuminate\Http\JsonResponse;

class PWASettingsController
{
    /**
     * Get PWA settings for the app download manager
     */
    public function getSettings(): JsonResponse
    {
        $settings = SchoolSetting::getInstance();
        
        // Determine icon URL - use custom PWA icon if available, otherwise use school logo
        $logoUrl = $settings->pwa_icon 
            ? url('storage/' . $settings->pwa_icon)
            : ($settings->school_logo ? url('storage/' . $settings->school_logo) : null);
        
        return response()->json([
            'appName' => $settings->pwa_app_name ?? $settings->school_name ?? 'School Management System',
            'shortName' => $settings->pwa_short_name ?? substr($settings->school_name ?? 'School', 0, 12),
            'logoUrl' => $logoUrl ?? url('/images/icon-192x192.png'),
            'school_name' => $settings->school_name,
            'pwa_icon' => $settings->pwa_icon,
            'school_logo' => $settings->school_logo,
        ]);
    }
}
