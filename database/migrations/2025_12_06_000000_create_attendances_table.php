<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_profile_id'); // Reference to UserProfile (student)
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('recorded_by')->nullable(); // Reference to User (teacher)
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('user_profile_id');
            $table->index('school_class_id');
            $table->index('date');
            $table->index('status');
            $table->unique(['user_profile_id', 'date'], 'unique_attendance');

            // Foreign key constraints
            $table->foreign('user_profile_id')
                ->references('id')
                ->on('user_profiles')
                ->onDelete('cascade');

            $table->foreign('school_class_id')
                ->references('id')
                ->on('school_classes')
                ->onDelete('cascade');

            $table->foreign('recorded_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
