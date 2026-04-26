<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include new event types
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('holiday', 'exam', 'break', 'meeting', 'celebration', 'term_begin', 'term_end', 'other') DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('holiday', 'exam', 'break', 'meeting', 'celebration', 'other') DEFAULT 'other'");
    }
};
