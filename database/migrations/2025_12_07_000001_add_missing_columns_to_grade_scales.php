<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grade_scales', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('grade_scales', 'remark')) {
                $table->string('remark')->nullable()->after('description');
            }
            if (!Schema::hasColumn('grade_scales', 'color')) {
                $table->string('color')->default('#000000')->after('remark');
            }
            if (!Schema::hasColumn('grade_scales', 'order')) {
                $table->integer('order')->default(0)->after('color');
            }
            if (!Schema::hasColumn('grade_scales', 'is_passing')) {
                $table->boolean('is_passing')->default(false)->after('order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('grade_scales', function (Blueprint $table) {
            $table->dropColumn(['remark', 'color', 'order', 'is_passing']);
        });
    }
};
