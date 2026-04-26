<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TIER 1: Fee items table - Master list of fee categories
        // These are reusable across all sessions and classes
        // Example: Tuition, ICT Fee, Exam Fee, Uniform, Books, etc.
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                      // "Tuition", "ICT Fee", etc.
            $table->text('description')->nullable();               // Description of what this fee is for
            $table->boolean('is_optional')->default(false);        // Is this fee optional?
            $table->decimal('default_amount', 12, 2)->nullable();  // Suggested default amount (informational)
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
        });

        // TIER 2: Fee structures table - Session + Class specific pricing
        // Defines the price list per academic session per school class
        // Example: JSS1 Tuition costs 45,000 in 2024/2025 session
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_session_id');     // Which session
            $table->unsignedBigInteger('school_class_id');         // Which class
            $table->unsignedBigInteger('fee_item_id');             // Which fee (FK to FeeItem)
            $table->decimal('amount', 12, 2);                      // Amount for this fee in this session/class
            $table->text('description')->nullable();               // Optional notes
            $table->boolean('is_active')->default(true);           // Is this pricing active?
            $table->timestamps();

            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('fee_item_id')->references('id')->on('fee_items')->onDelete('cascade');

            // Unique constraint: One fee per class per session
            $table->unique(['academic_session_id', 'school_class_id', 'fee_item_id'], 'fee_structures_unique');

            $table->index('academic_session_id');
            $table->index('school_class_id');
            $table->index('fee_item_id');
            $table->index('is_active');
        });

        // TIER 3: Bill items table - Student invoices
        // Auto-generated from fee_structures
        // One record = one line item on a student's bill for one fee for one session/class
        // Example: Student#123 owes 45,000 for JSS1 Tuition in 2024/2025 session
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');              // Which student
            $table->unsignedBigInteger('academic_session_id');     // Which session
            $table->unsignedBigInteger('school_class_id');         // Which class
            $table->unsignedBigInteger('fee_item_id');             // Which fee
            $table->unsignedBigInteger('fee_structure_id');        // Reference to the source fee_structure
            $table->decimal('amount', 12, 2);                      // Amount owed
            $table->decimal('paid_amount', 12, 2)->default(0);     // Amount paid so far
            $table->enum('status', ['unpaid', 'paid', 'partial'])->default('unpaid');
            $table->date('due_date')->nullable();                  // When payment is due
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('fee_item_id')->references('id')->on('fee_items')->onDelete('cascade');
            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');

            // Unique constraint: One bill line per student per fee per session/class
            $table->unique(['student_id', 'academic_session_id', 'school_class_id', 'fee_item_id'], 'bill_items_unique');

            $table->index('student_id');
            $table->index('academic_session_id');
            $table->index('school_class_id');
            $table->index('fee_item_id');
            $table->index('status');
        });

        // Payments table - Records actual payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();            // Unique transaction reference
            $table->unsignedBigInteger('student_id');              // Which student
            $table->unsignedBigInteger('bill_item_id');            // Which bill item is being paid
            $table->decimal('amount', 12, 2);                      // Amount paid
            $table->string('currency')->default('NGN');            // Currency
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'card', 'momo', 'online', 'waiver'])
                ->default('cash');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->text('remarks')->nullable();                   // Payment notes
            $table->unsignedBigInteger('recorded_by')->nullable(); // Who recorded this payment
            $table->dateTime('payment_date')->nullable();          // When payment was made
            $table->dateTime('verified_at')->nullable();           // When payment was verified
            $table->unsignedBigInteger('verified_by')->nullable(); // Who verified
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('bill_item_id')->references('id')->on('bill_items')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');

            $table->index('student_id');
            $table->index('bill_item_id');
            $table->index('status');
            $table->index('payment_date');
        });

        // Payment receipts table - For printing receipts
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('issued_by');
            $table->text('notes')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->dateTime('printed_at')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('restrict');

            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_items');
    }
};
