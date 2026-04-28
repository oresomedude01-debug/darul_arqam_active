<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('school_settings', 'pwa_app_name')) {
                $table->string('pwa_app_name')->nullable()->after('school_logo')->comment('PWA app name');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_short_name')) {
                $table->string('pwa_short_name')->nullable()->after('pwa_app_name')->comment('PWA short name');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_icon')) {
                $table->string('pwa_icon')->nullable()->after('pwa_short_name')->comment('PWA app icon path');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_theme_color')) {
                $table->string('pwa_theme_color')->nullable()->default('#0284c7')->after('pwa_icon')->comment('PWA theme color');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_background_color')) {
                $table->string('pwa_background_color')->nullable()->default('#ffffff')->after('pwa_theme_color')->comment('PWA background color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (Schema::hasColumn('school_settings', 'pwa_app_name')) {
                $table->dropColumn('pwa_app_name');
            }
            if (Schema::hasColumn('school_settings', 'pwa_short_name')) {
                $table->dropColumn('pwa_short_name');
            }
            if (Schema::hasColumn('school_settings', 'pwa_icon')) {
                $table->dropColumn('pwa_icon');
            }
            if (Schema::hasColumn('school_settings', 'pwa_theme_color')) {
                $table->dropColumn('pwa_theme_color');
            }
            if (Schema::hasColumn('school_settings', 'pwa_background_color')) {
                $table->dropColumn('pwa_background_color');
            }
        });
    }
};
