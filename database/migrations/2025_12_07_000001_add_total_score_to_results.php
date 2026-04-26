<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // If score column exists and total_score doesn't, rename it
            if (Schema::hasColumn('results', 'score') && !Schema::hasColumn('results', 'total_score')) {
                $table->renameColumn('score', 'total_score');
            } elseif (!Schema::hasColumn('results', 'total_score')) {
                // If neither exists, create total_score
                $table->decimal('total_score', 5, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            if (Schema::hasColumn('results', 'total_score')) {
                $table->renameColumn('total_score', 'score');
            }
        });
    }
};
