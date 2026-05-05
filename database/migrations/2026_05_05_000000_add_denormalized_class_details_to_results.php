<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add denormalized class details to results table
     * This preserves the state of the class at the time the exam was written
     * and prevents changes to class/subject/enrollment from affecting historical results
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // Denormalized subject data (snapshot at exam time)
            if (!Schema::hasColumn('results', 'subject_name_snapshot')) {
                $table->string('subject_name_snapshot')->nullable()->after('subject_id')->comment('Subject name at time of exam');
            }
            if (!Schema::hasColumn('results', 'subject_code_snapshot')) {
                $table->string('subject_code_snapshot')->nullable()->after('subject_name_snapshot')->comment('Subject code at time of exam');
            }

            // Denormalized class data (snapshot at exam time)
            if (!Schema::hasColumn('results', 'class_name_snapshot')) {
                $table->string('class_name_snapshot')->nullable()->after('school_class_id')->comment('Class name at time of exam');
            }
            if (!Schema::hasColumn('results', 'class_students_count_snapshot')) {
                $table->integer('class_students_count_snapshot')->default(0)->after('class_name_snapshot')->comment('Number of students in class at time of exam');
            }

            // Store which subjects were being taught in this class at exam time (as JSON)
            if (!Schema::hasColumn('results', 'class_subjects_snapshot')) {
                $table->json('class_subjects_snapshot')->nullable()->after('class_students_count_snapshot')->comment('Subjects taught in class at time of exam');
            }

            // Additional immutable data for audit trail
            if (!Schema::hasColumn('results', 'student_name_snapshot')) {
                $table->string('student_name_snapshot')->nullable()->after('student_id')->comment('Student name at time of exam');
            }
            if (!Schema::hasColumn('results', 'student_admission_no_snapshot')) {
                $table->string('student_admission_no_snapshot')->nullable()->after('student_name_snapshot')->comment('Student admission number at time of exam');
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $columnsToDelete = [
                'subject_name_snapshot',
                'subject_code_snapshot',
                'class_name_snapshot',
                'class_students_count_snapshot',
                'class_subjects_snapshot',
                'student_name_snapshot',
                'student_admission_no_snapshot',
            ];

            foreach ($columnsToDelete as $column) {
                if (Schema::hasColumn('results', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
