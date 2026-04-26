<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tables if they exist
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('student_bills');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_items');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_receipts');
        Schema::enableForeignKeyConstraints();

        // 1. FEE ITEMS - Master list of fee components
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Tuition Fee", "ICT Fee"
            $table->text('description')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('is_optional');
        });

        // 2. FEE STRUCTURES - Template for fees per class/session/term
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_session_id');
            $table->unsignedBigInteger('academic_term_id')->nullable();
            $table->unsignedBigInteger('school_class_id');
            $table->string('name'); // e.g., "JSS1 - 2024/2025 - Term 1"
            $table->text('description')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');

            $table->unique(['academic_session_id', 'academic_term_id', 'school_class_id'], 'fee_structures_unique');
            $table->index('academic_session_id');
            $table->index('school_class_id');
            $table->index('is_active');
        });

        // 3. STRUCTURE ITEMS - Fees attached to a structure
        Schema::create('fee_structure_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_structure_id');
            $table->unsignedBigInteger('fee_item_id');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');
            $table->foreign('fee_item_id')->references('id')->on('fee_items')->onDelete('cascade');

            $table->unique(['fee_structure_id', 'fee_item_id'], 'structure_items_unique');
            $table->index('fee_structure_id');
        });

        // 4. STUDENT BILLS - Individual bills per student
        Schema::create('student_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_session_id');
            $table->unsignedBigInteger('academic_term_id')->nullable();
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('fee_structure_id');
            $table->string('bill_number')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('issued_at')->default(now());
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');

            $table->unique(['student_id', 'academic_session_id', 'academic_term_id', 'school_class_id'], 'student_bills_unique');
            $table->index('student_id');
            $table->index('status');
            $table->index('due_date');
        });

        // 5. BILL ITEMS - Individual fee items on a bill
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_bill_id');
            $table->unsignedBigInteger('fee_item_id');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('student_bill_id')->references('id')->on('student_bills')->onDelete('cascade');
            $table->foreign('fee_item_id')->references('id')->on('fee_items')->onDelete('cascade');

            $table->index('student_bill_id');
        });

        // 6. PAYMENTS - Track all payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_bill_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'paystack', 'waiver'])->default('cash');
            $table->enum('status', ['pending', 'verified', 'failed', 'cancelled'])->default('verified');
            $table->string('reference_number')->unique();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('paid_at');
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_bill_id')->references('id')->on('student_bills')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');

            $table->index('student_id');
            $table->index('status');
            $table->index('paid_at');
        });

        // 7. PAYMENT RECEIPTS - Generate receipts for payments
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->string('receipt_number')->unique();
            $table->enum('status', ['generated', 'printed', 'sent', 'void'])->default('generated');
            $table->text('notes')->nullable();
            $table->dateTime('generated_at')->default(now());
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');

            $table->index('receipt_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('student_bills');
        Schema::dropIfExists('fee_structure_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_items');
        Schema::enableForeignKeyConstraints();
    }
};
