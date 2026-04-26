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
        // Disable foreign key constraints temporarily
        Schema::disableForeignKeyConstraints();

        // Drop dependent tables first
        Schema::dropIfExists('fee_structure_items');
        Schema::dropIfExists('fee_structures');

        // Create new fee_structures table as templates (no class requirement)
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Standard Tuition 2024/2025", "ICT Fee Package"
            $table->text('description')->nullable();
            $table->unsignedBigInteger('academic_session_id')->nullable(); // Optional: if tied to specific session
            $table->unsignedBigInteger('academic_term_id')->nullable();    // Optional: if tied to specific term
            $table->decimal('total_amount', 12, 2)->default(0); // Auto-calculated from items
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys (both optional for flexibility)
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('set null');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->onDelete('set null');

            $table->index('academic_session_id');
            $table->index('is_active');
        });

        // Re-create fee_structure_items
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

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Rollback will require recreating the old structure
        // This is a one-way migration to convert to template model
    }
};
