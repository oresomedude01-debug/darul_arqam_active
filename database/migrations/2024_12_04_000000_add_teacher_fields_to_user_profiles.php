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
        Schema::table('user_profiles', function (Blueprint $table) {
            // Add teacher-specific fields if they don't exist
            if (!Schema::hasColumn('user_profiles', 'subjects')) {
                $table->json('subjects')->nullable()->after('specialization');
            }
            if (!Schema::hasColumn('user_profiles', 'classes')) {
                $table->json('classes')->nullable()->after('subjects');
            }
            if (!Schema::hasColumn('user_profiles', 'date_joined')) {
                $table->date('date_joined')->nullable()->after('employment_date');
            }
            if (!Schema::hasColumn('user_profiles', 'notes')) {
                $table->text('notes')->nullable()->after('date_joined');
            }
            if (!Schema::hasColumn('user_profiles', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('user_profiles', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('user_profiles', 'country')) {
                $table->string('country')->nullable()->after('state');
            }
            if (!Schema::hasColumn('user_profiles', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $columns = ['subjects', 'classes', 'date_joined', 'notes', 'city', 'state', 'country', 'profile_picture'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('user_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
