<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_items', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('fee_items', 'is_optional')) {
                $table->boolean('is_optional')->default(false)->after('description');
            }
            if (!Schema::hasColumn('fee_items', 'default_amount')) {
                $table->decimal('default_amount', 12, 2)->nullable()->after('is_optional');
            }
            if (!Schema::hasColumn('fee_items', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('default_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_items', function (Blueprint $table) {
            if (Schema::hasColumn('fee_items', 'is_optional')) {
                $table->dropColumn('is_optional');
            }
            if (Schema::hasColumn('fee_items', 'default_amount')) {
                $table->dropColumn('default_amount');
            }
            if (Schema::hasColumn('fee_items', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
