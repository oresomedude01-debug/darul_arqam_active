<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add critical performance indexes for faster query execution
     * These indexes optimize N+1 query patterns and aggregation queries
     */
    public function up(): void
    {
        // Results table - frequently queried by student/subject/session
        if (Schema::hasTable('results')) {
            Schema::table('results', function (Blueprint $table) {
                if (!$this->hasIndex('results', 'results_academic_session_academic_term_index')) {
                    $table->index(['academic_session_id', 'academic_term_id'], 'results_academic_session_academic_term_index');
                }
                if (!$this->hasIndex('results', 'results_student_subject_index')) {
                    $table->index(['student_id', 'subject_id'], 'results_student_subject_index');
                }
                if (!$this->hasIndex('results', 'idx_results_total_score')) {
                    $table->index('total_score');
                }
            });
        }

        // Class Subjects - optimize teacher lookups
        if (Schema::hasTable('class_subjects')) {
            Schema::table('class_subjects', function (Blueprint $table) {
                if (!$this->hasIndex('class_subjects', 'class_subjects_teacher_id_index')) {
                    $table->index('teacher_id');
                }
                if (!$this->hasIndex('class_subjects', 'class_subjects_school_class_teacher_index')) {
                    $table->index(['school_class_id', 'teacher_id'], 'class_subjects_school_class_teacher_index');
                }
            });
        }

        // User Roles - speed up role lookups for authorization
        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table) {
                if (!$this->hasIndex('user_roles', 'user_roles_role_id_index')) {
                    $table->index('role_id');
                }
            });
        }

        // Attendance - optimize attendance queries
        if (Schema::hasTable('attendance')) {
            Schema::table('attendance', function (Blueprint $table) {
                if (!$this->hasIndex('attendance', 'attendance_user_profile_status_index')) {
                    $table->index(['user_profile_id', 'status'], 'attendance_user_profile_status_index');
                }
                if (!$this->hasIndex('attendance', 'attendance_attendance_date_index')) {
                    $table->index('attendance_date');
                }
            });
        }

        // Student Bills - payment/billing queries
        if (Schema::hasTable('student_bills')) {
            Schema::table('student_bills', function (Blueprint $table) {
                if (!$this->hasIndex('student_bills', 'student_bills_student_status_index')) {
                    $table->index(['student_id', 'status'], 'student_bills_student_status_index');
                }
                if (!$this->hasIndex('student_bills', 'student_bills_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }

        // Payments - transaction history queries
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!$this->hasIndex('payments', 'payments_student_created_index')) {
                    $table->index(['student_id', 'created_at'], 'payments_student_created_index');
                }
                if (!$this->hasIndex('payments', 'payments_status_index')) {
                    $table->index('status');
                }
            });
        }

        // Timetable - class schedule lookups
        if (Schema::hasTable('time_tables')) {
            Schema::table('time_tables', function (Blueprint $table) {
                if (!$this->hasIndex('time_tables', 'time_tables_school_class_index')) {
                    $table->index('school_class_id');
                }
                if (!$this->hasIndex('time_tables', 'time_tables_subject_index')) {
                    $table->index('subject_id');
                }
            });
        }

        // Blogs - featured/published lookups
        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                if (!$this->hasIndex('blogs', 'blogs_status_published_index')) {
                    $table->index(['status', 'published_at']);
                }
                if (!$this->hasIndex('blogs', 'blogs_slug_index')) {
                    $table->index('slug');
                }
            });
        }

        // User Profiles - frequently filtered by status and class
        if (Schema::hasTable('user_profiles')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                if (!$this->hasIndex('user_profiles', 'user_profiles_school_class_status_index')) {
                    $table->index(['school_class_id', 'status']);
                }
                if (!$this->hasIndex('user_profiles', 'user_profiles_parent_index')) {
                    $table->index('parent_id');
                }
            });
        }
    }

    public function down(): void
    {
        // Drop all the indexes we added (MySQL handles this automatically on down)
        // This is optional but safer to not drop in case of issues
    }

    /**
     * Helper method to check if an index exists
     */
    private function hasIndex($table, $indexName): bool
    {
        try {
            $indexedColumns = \DB::select(
                "SELECT * FROM INFORMATION_SCHEMA.STATISTICS 
                 WHERE TABLE_NAME = ? AND INDEX_NAME = ? AND TABLE_SCHEMA = DATABASE()",
                [$table, $indexName]
            );
            return !empty($indexedColumns);
        } catch (\Exception $e) {
            return false;
        }
    }
};
