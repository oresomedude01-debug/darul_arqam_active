<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('student_bills', 'description')) {
                $table->string('description')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_bills', function (Blueprint $table) {
            if (Schema::hasColumn('student_bills', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
