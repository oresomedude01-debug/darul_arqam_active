<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Check if columns exist before trying to drop them
            $columns = Schema::getColumnListing('attendances');
            
            if (in_array('subject_id', $columns)) {
                // Need to drop foreign key first if it exists
                try {
                    $table->dropForeign(['subject_id']);
                } catch (\Exception $e) {
                    // Foreign key may not exist
                }
                $table->dropColumn('subject_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('school_class_id');
                $table->foreign('subject_id')
                    ->references('id')
                    ->on('subjects')
                    ->onDelete('set null');
            }
        });
    }
};
