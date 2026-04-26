<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->dateTime('payment_date')->nullable()->after('paid_at');
            }
        });

        // Copy paid_at to payment_date for existing records
        DB::statement('UPDATE payments SET payment_date = paid_at WHERE payment_date IS NULL');
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
        });
    }
};
