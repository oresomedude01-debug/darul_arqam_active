<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('school_settings', 'paystack_public_key')) {
                $table->string('paystack_public_key')->nullable()->after('enable_online_payment');
            }
            if (!Schema::hasColumn('school_settings', 'paystack_secret_key')) {
                $table->string('paystack_secret_key')->nullable()->after('paystack_public_key');
            }
            if (!Schema::hasColumn('school_settings', 'paystack_merchant_email')) {
                $table->string('paystack_merchant_email')->nullable()->after('paystack_secret_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (Schema::hasColumn('school_settings', 'paystack_public_key')) {
                $table->dropColumn('paystack_public_key');
            }
            if (Schema::hasColumn('school_settings', 'paystack_secret_key')) {
                $table->dropColumn('paystack_secret_key');
            }
            if (Schema::hasColumn('school_settings', 'paystack_merchant_email')) {
                $table->dropColumn('paystack_merchant_email');
            }
        });
    }
};
