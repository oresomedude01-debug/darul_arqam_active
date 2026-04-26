<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simplify billing to core essentials:
     * - Fee Structures: Template only (session + term + items)
     * - Student Bills: One bill per student per class/session/term
     * - Payments: Simple cash/transfer/online tracking
     * - No bill_items complexity - just total and itemization is read from structure
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop complex tables
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_bills');

        // Recreate student_bills - simplified
        Schema::create('student_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_session_id');
            $table->unsignedBigInteger('academic_term_id');
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('fee_structure_id');
            
            // Amounts
            $table->decimal('total_amount', 12, 2);      // From fee structure items
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2);       // total - paid
            
            // Status
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');

            // Unique: one bill per student per session/term/class
            $table->unique(['student_id', 'academic_session_id', 'academic_term_id', 'school_class_id'], 'student_bills_unique');
            
            $table->index('student_id');
            $table->index('academic_session_id');
            $table->index('status');
        });

        // Payments - simple tracking
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_bill_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'online', 'waiver'])->default('cash');
            $table->string('reference_number')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->dateTime('paid_at');
            $table->unsignedBigInteger('recorded_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_bill_id')->references('id')->on('student_bills')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');

            $table->index('student_bill_id');
            $table->index('paid_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_bills');
        Schema::enableForeignKeyConstraints();
    }
};
