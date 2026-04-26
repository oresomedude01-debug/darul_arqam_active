<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            // Add financial fields if they don't exist
            if (!Schema::hasColumn('school_settings', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('default_currency');
            }
            if (!Schema::hasColumn('school_settings', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('school_settings', 'account_number')) {
                $table->string('account_number')->nullable()->after('account_holder_name');
            }
            if (!Schema::hasColumn('school_settings', 'account_type')) {
                $table->string('account_type')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('school_settings', 'bank_code')) {
                $table->string('bank_code')->nullable()->after('account_type');
            }
            if (!Schema::hasColumn('school_settings', 'routing_number')) {
                $table->string('routing_number')->nullable()->after('bank_code');
            }
            if (!Schema::hasColumn('school_settings', 'swift_code')) {
                $table->string('swift_code')->nullable()->after('routing_number');
            }
            if (!Schema::hasColumn('school_settings', 'iban')) {
                $table->string('iban')->nullable()->after('swift_code');
            }
            if (!Schema::hasColumn('school_settings', 'paystack_public_key')) {
                $table->string('paystack_public_key')->nullable()->after('iban');
            }
            if (!Schema::hasColumn('school_settings', 'paystack_secret_key')) {
                $table->string('paystack_secret_key')->nullable()->after('paystack_public_key');
            }
            if (!Schema::hasColumn('school_settings', 'paystack_merchant_email')) {
                $table->string('paystack_merchant_email')->nullable()->after('paystack_secret_key');
            }
            if (!Schema::hasColumn('school_settings', 'default_payment_method')) {
                $table->string('default_payment_method')->default('cash')->after('paystack_merchant_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $columns = [
                'bank_name',
                'account_holder_name',
                'account_number',
                'account_type',
                'bank_code',
                'routing_number',
                'swift_code',
                'iban',
                'paystack_public_key',
                'paystack_secret_key',
                'paystack_merchant_email',
                'default_payment_method',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('school_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
