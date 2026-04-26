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
        // Registration tokens table
        Schema::create('registration_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('status', ['active', 'expired', 'consumed', 'disabled'])->default('active');
            $table->string('session_year')->nullable();
            $table->string('class_level')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('consumed_at')->nullable();
            $table->string('consumed_by_ip')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index('code');
            $table->index('status');
            $table->index('session_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_tokens');
    }
};
