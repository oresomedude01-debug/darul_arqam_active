<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Academic sessions table
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session')->unique();
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Academic terms table - MERGED with all columns
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_session_id');
            $table->string('name');
            $table->string('session');
            $table->enum('term', ['First Term', 'Second Term', 'Third Term']);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('academic_session_id')
                ->references('id')
                ->on('academic_sessions')
                ->onDelete('cascade');

            $table->index('academic_session_id');
            $table->index('term');
            $table->index('status');
        });

        // Grade scales table
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade')->unique();
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Exam types table
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->decimal('weight', 5, 2)->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('academic_sessions');
    }
};
