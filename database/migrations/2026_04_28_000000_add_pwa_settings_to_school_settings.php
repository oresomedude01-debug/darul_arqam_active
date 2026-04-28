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
                $table->string('pwa_app_name')->nullable()->after('school_name');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_short_name')) {
                $table->string('pwa_short_name', 12)->nullable()->after('pwa_app_name');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_icon')) {
                $table->string('pwa_icon')->nullable()->after('pwa_short_name')->comment('Custom PWA icon - if empty, uses school_logo');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_theme_color')) {
                $table->string('pwa_theme_color', 7)->default('#0284c7')->after('pwa_icon');
            }
            if (!Schema::hasColumn('school_settings', 'pwa_background_color')) {
                $table->string('pwa_background_color', 7)->default('#ffffff')->after('pwa_theme_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn([
                'pwa_app_name',
                'pwa_short_name',
                'pwa_icon',
                'pwa_theme_color',
                'pwa_background_color',
            ]);
        });
    }
};
