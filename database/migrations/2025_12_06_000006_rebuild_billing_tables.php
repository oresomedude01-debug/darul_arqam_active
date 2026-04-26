<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks temporarily
        Schema::disableForeignKeyConstraints();

        // Drop existing tables completely to rebuild them fresh
        if (Schema::hasTable('bill_items')) {
            Schema::dropIfExists('bill_items');
        }
        if (Schema::hasTable('fee_structures')) {
            Schema::dropIfExists('fee_structures');
        }
        if (Schema::hasTable('fee_items')) {
            Schema::dropIfExists('fee_items');
        }

        // Update payments table to remove old student_bill_id references
        if (Schema::hasTable('payments')) {
            if (Schema::hasColumn('payments', 'student_bill_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    try {
                        $table->dropForeign(['student_bill_id']);
                    } catch (\Exception $e) {
                        // ignore if doesn't exist
                    }
                    $table->dropColumn('student_bill_id');
                });
            }
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // TIER 1: Create fee_items table - Master list
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

        // TIER 2: Create fee_structures table - Session + Class pricing
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

        // TIER 3: Create bill_items table - Student invoices
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

        // Add bill_item_id to payments table if it doesn't exist
        if (Schema::hasTable('payments')) {
            if (!Schema::hasColumn('payments', 'bill_item_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->unsignedBigInteger('bill_item_id')->nullable()->after('student_id');
                    $table->foreign('bill_item_id')->references('id')->on('bill_items')->onDelete('cascade');
                    $table->index('bill_item_id');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_items');

        if (Schema::hasTable('payments')) {
            if (Schema::hasColumn('payments', 'bill_item_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    try {
                        $table->dropForeign(['bill_item_id']);
                    } catch (\Exception $e) {
                        // ignore
                    }
                    $table->dropColumn('bill_item_id');
                });
            }
        }
    }
};
