<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Personal Information
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            
            // Identification
            $table->string('admission_number')->nullable()->unique();
            $table->unsignedBigInteger('registration_token_id')->nullable();
            
            // Personal Details
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('nationality')->nullable();
            $table->string('state_of_origin')->nullable();
            
            // Student Related
            $table->unsignedBigInteger('school_class_id')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('previous_school')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'graduated', 'transferred'])->default('active');
            
            // Parent/Guardian Related
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('relationship')->nullable(); // Son, Daughter, Ward, etc.
            
            // Staff Related
            $table->string('occupation')->nullable(); // Teacher, Admin, Staff, etc.
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->date('employment_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('admission_number');
            $table->index('school_class_id');
            $table->index('parent_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
