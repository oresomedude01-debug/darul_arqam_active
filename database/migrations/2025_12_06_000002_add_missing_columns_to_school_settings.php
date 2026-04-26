<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('school_settings', 'school_days')) {
                $table->json('school_days')->nullable()->after('holiday_dates');
            }
            if (!Schema::hasColumn('school_settings', 'grade_boundaries')) {
                $table->json('grade_boundaries')->nullable()->after('grade_scale');
            }
            if (!Schema::hasColumn('school_settings', 'promotion_settings')) {
                $table->json('promotion_settings')->nullable()->after('number_of_terms');
            }
            if (!Schema::hasColumn('school_settings', 'term_start_date')) {
                $table->date('term_start_date')->nullable()->after('session_end_date');
            }
            if (!Schema::hasColumn('school_settings', 'term_end_date')) {
                $table->date('term_end_date')->nullable()->after('term_start_date');
            }
            if (!Schema::hasColumn('school_settings', 'teachers_can_enter_scores')) {
                $table->boolean('teachers_can_enter_scores')->default(true)->after('send_email_notifications');
            }
            if (!Schema::hasColumn('school_settings', 'parents_can_view_results')) {
                $table->boolean('parents_can_view_results')->default(true)->after('teachers_can_enter_scores');
            }
            if (!Schema::hasColumn('school_settings', 'parents_can_view_attendance')) {
                $table->boolean('parents_can_view_attendance')->default(true)->after('parents_can_view_results');
            }
            if (!Schema::hasColumn('school_settings', 'require_daily_attendance')) {
                $table->boolean('require_daily_attendance')->default(true)->after('parents_can_view_attendance');
            }
            if (!Schema::hasColumn('school_settings', 'enable_notifications')) {
                $table->boolean('enable_notifications')->default(true)->after('require_daily_attendance');
            }
            if (!Schema::hasColumn('school_settings', 'enable_fees_module')) {
                $table->boolean('enable_fees_module')->default(false)->after('enable_notifications');
            }
            if (!Schema::hasColumn('school_settings', 'enable_library_module')) {
                $table->boolean('enable_library_module')->default(false)->after('enable_fees_module');
            }
            if (!Schema::hasColumn('school_settings', 'enable_online_payment')) {
                $table->boolean('enable_online_payment')->default(false)->after('enable_library_module');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $columns = [
                'school_days',
                'grade_boundaries',
                'promotion_settings',
                'term_start_date',
                'term_end_date',
                'teachers_can_enter_scores',
                'parents_can_view_results',
                'parents_can_view_attendance',
                'require_daily_attendance',
                'enable_notifications',
                'enable_fees_module',
                'enable_library_module',
                'enable_online_payment',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('school_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
