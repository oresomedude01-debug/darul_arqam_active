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
            $table->json('allergies')->nullable()->after('medical_conditions');
            $table->text('medications')->nullable()->after('allergies');
            $table->boolean('emergency_medical_consent')->default(false)->after('medications');
            $table->text('special_needs')->nullable()->after('emergency_medical_consent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['allergies', 'medications', 'emergency_medical_consent', 'special_needs']);
        });
    }
};
