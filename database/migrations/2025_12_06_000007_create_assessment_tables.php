<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {


        // Results table - MERGED with assessment_ratings and individual_assessments
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('school_class_id')->nullable();
            $table->unsignedBigInteger('academic_session_id')->nullable();
            $table->unsignedBigInteger('academic_term_id')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->integer('position_in_class')->nullable();
            $table->integer('position_in_subject')->nullable();
            $table->text('remarks')->nullable();
            $table->json('assessment_ratings')->nullable();
            $table->json('individual_assessments')->nullable();
            $table->boolean('released')->default(false);
            $table->dateTime('released_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // student_id references user_profiles (StudentUserProfile)
            $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('set null');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('set null');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->onDelete('set null');

            $table->unique(['student_id', 'subject_id', 'academic_term_id'], 'results_student_subject_term_unique');
            $table->index('student_id');
            $table->index('school_class_id');
            $table->index('academic_term_id');
            $table->index('released');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
