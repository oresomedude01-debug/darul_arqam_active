<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // School settings table - MERGED with all configuration columns
        if (!Schema::hasTable('school_settings')) {
            Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->text('school_address')->nullable();
            $table->string('school_phone')->nullable();
            $table->string('school_email')->nullable();
            $table->string('school_website')->nullable();
            $table->text('school_logo')->nullable();
            $table->string('school_motto')->nullable();
            $table->text('school_vision')->nullable();
            $table->text('school_mission')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('vice_principal_name')->nullable();
            $table->text('footer_text')->nullable();
            
            // Academic configuration
            $table->unsignedBigInteger('active_session_id')->nullable();
            $table->unsignedBigInteger('active_term_id')->nullable();
            $table->string('grading_system')->default('A-F');
            $table->json('grade_scale')->nullable();
            
            // Term/Session management
            $table->integer('number_of_terms')->default(3);
            $table->date('session_start_date')->nullable();
            $table->date('session_end_date')->nullable();
            
            // School calendar
            $table->json('school_calendar')->nullable();
            $table->json('holiday_dates')->nullable();
            
            // System preferences
            $table->boolean('enable_parent_portal')->default(true);
            $table->boolean('enable_student_portal')->default(true);
            $table->boolean('enable_online_payments')->default(false);
            $table->string('default_currency')->default('NGN');
            $table->string('timezone')->default('Africa/Lagos');
            
            // Configuration flags
            $table->boolean('require_photo_on_registration')->default(true);
            $table->boolean('require_parent_approval_for_student_registration')->default(true);
            $table->boolean('allow_bulk_grade_upload')->default(true);
            $table->boolean('send_sms_notifications')->default(true);
            $table->boolean('send_email_notifications')->default(true);
            
            // Additional settings
            $table->text('additional_settings')->nullable();
            $table->timestamps();

            $table->foreign('active_session_id')->references('id')->on('academic_sessions')->onDelete('set null');
            $table->foreign('active_term_id')->references('id')->on('academic_terms')->onDelete('set null');
            });
        }

        // System activity log
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index('user_id');
            $table->index('action');
            $table->index('entity_type');
            $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');


        Schema::dropIfExists('school_settings');
    }
};
