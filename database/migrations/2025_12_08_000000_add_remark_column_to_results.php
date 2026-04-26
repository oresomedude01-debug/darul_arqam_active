<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // Add remark column if it doesn't exist
            if (!Schema::hasColumn('results', 'remark')) {
                $table->string('remark')->nullable()->after('grade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            if (Schema::hasColumn('results', 'remark')) {
                $table->dropColumn('remark');
            }
        });
    }
};
