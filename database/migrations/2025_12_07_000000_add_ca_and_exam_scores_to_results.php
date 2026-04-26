<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // Add individual score columns if they don't exist
            if (!Schema::hasColumn('results', 'ca_score')) {
                $table->decimal('ca_score', 5, 2)->nullable()->after('score');
            }
            if (!Schema::hasColumn('results', 'exam_score')) {
                $table->decimal('exam_score', 5, 2)->nullable()->after('ca_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['ca_score', 'exam_score']);
        });
    }
};
