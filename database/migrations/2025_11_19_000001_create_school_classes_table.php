<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('section')->nullable();
            $table->string('class_code')->unique();
            $table->json('subject_teachers')->nullable();
            $table->integer('capacity')->default(40);
            $table->string('room_number')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->text('description')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->index('class_code');
            $table->index('status');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
