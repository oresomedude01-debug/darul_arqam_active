<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add waiver to payment_method enum
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support MODIFY, so we skip this for SQLite
            return;
        }
        
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'cheque', 'card', 'momo', 'online', 'waiver') DEFAULT 'cash'");
    }

    public function down(): void
    {
        // Remove waiver from payment_method enum
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support MODIFY, so we skip this for SQLite
            return;
        }
        
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'cheque', 'card', 'momo', 'online') DEFAULT 'cash'");
    }
};
