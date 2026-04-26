<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->string('name')->nullable()->after('description');
            $table->enum('type', ['tuition', 'registration', 'uniform', 'books', 'technology', 'activities', 'other'])->nullable()->after('name');
            $table->string('currency', 3)->default('NGN')->after('amount');
            $table->date('due_date')->nullable()->after('currency');
            $table->integer('installments')->default(1)->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn(['name', 'type', 'currency', 'due_date', 'installments']);
        });
    }
};
