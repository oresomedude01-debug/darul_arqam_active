<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration updates the financial schema from StudentBill-based
     * to BillItem-based (3-tier industry-standard billing).
     */
    public function up(): void
    {
        // Check if fee_items table exists and has the old structure
        if (Schema::hasTable('fee_items') && !Schema::hasColumn('fee_items', 'is_optional')) {
            // Drop old fee_items table and recreate with new structure
            Schema::drop('fee_items');
        }

        // Recreate fee_items with new structure if it doesn't exist
        if (!Schema::hasTable('fee_items')) {
            Schema::create('fee_items', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_optional')->default(false);
                $table->decimal('default_amount', 12, 2)->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->index('status');
            });
        }

        // Recreate fee_structures with new structure if it doesn't exist
        if (!Schema::hasTable('fee_structures')) {
            Schema::create('fee_structures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academic_session_id');
                $table->unsignedBigInteger('school_class_id');
                $table->unsignedBigInteger('fee_item_id');
                $table->decimal('amount', 12, 2);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
                $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
                $table->foreign('fee_item_id')->references('id')->on('fee_items')->onDelete('cascade');

                $table->unique(['academic_session_id', 'school_class_id', 'fee_item_id'], 'fee_structures_unique');
                $table->index('academic_session_id');
                $table->index('school_class_id');
                $table->index('fee_item_id');
                $table->index('is_active');
            });
        }

        // Create bill_items table (student invoices)
        if (!Schema::hasTable('bill_items')) {
            Schema::create('bill_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('academic_session_id');
                $table->unsignedBigInteger('school_class_id');
                $table->unsignedBigInteger('fee_item_id');
                $table->unsignedBigInteger('fee_structure_id');
                $table->decimal('amount', 12, 2);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->enum('status', ['unpaid', 'paid', 'partial'])->default('unpaid');
                $table->date('due_date')->nullable();
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('user_profiles')->onDelete('cascade');
                $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
                $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
                $table->foreign('fee_item_id')->references('id')->on('fee_items')->onDelete('cascade');
                $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');

                $table->unique(['student_id', 'academic_session_id', 'school_class_id', 'fee_item_id'], 'bill_items_unique');
                $table->index('student_id');
                $table->index('academic_session_id');
                $table->index('school_class_id');
                $table->index('fee_item_id');
                $table->index('status');
            });
        }

        // Update payments table to use bill_item_id instead of student_bill_id
        if (Schema::hasTable('payments')) {
            // Check if it has the old student_bill_id column
            if (Schema::hasColumn('payments', 'student_bill_id')) {
                // Drop the old foreign key if it exists
                Schema::table('payments', function (Blueprint $table) {
                    // Drop old FK
                    try {
                        $table->dropForeign(['student_bill_id']);
                    } catch (\Exception $e) {
                        // FK doesn't exist, continue
                    }
                    // Drop old column
                    $table->dropColumn('student_bill_id');
                });
            }

            // Add bill_item_id if it doesn't exist
            if (!Schema::hasColumn('payments', 'bill_item_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->unsignedBigInteger('bill_item_id')->nullable()->after('student_id');
                    $table->foreign('bill_item_id')->references('id')->on('bill_items')->onDelete('cascade');
                    $table->index('bill_item_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_items');
    }
};
