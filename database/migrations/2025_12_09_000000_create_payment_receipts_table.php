<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create payment_receipts table if it doesn't exist
        if (!Schema::hasTable('payment_receipts')) {
            Schema::create('payment_receipts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payment_id');
                $table->string('receipt_number')->unique();
                $table->enum('status', ['generated', 'printed', 'sent', 'void'])->default('generated');
                $table->text('notes')->nullable();
                $table->dateTime('generated_at')->default(now());
                $table->timestamps();

                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');

                $table->index('receipt_number');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
