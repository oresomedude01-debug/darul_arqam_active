<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('results', 'teacher_comment')) {
                $table->text('teacher_comment')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('results', 'class_teacher_comment')) {
                $table->text('class_teacher_comment')->nullable()->after('teacher_comment');
            }
            if (!Schema::hasColumn('results', 'status')) {
                $table->string('status')->nullable()->after('class_teacher_comment');
            }
            if (!Schema::hasColumn('results', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('results', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('teacher_id');
            }
            if (!Schema::hasColumn('results', 'submitted_at')) {
                $table->dateTime('submitted_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('results', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('results', 'is_released')) {
                $table->boolean('is_released')->default(false)->after('approved_at');
            }
            
            // Individual assessment columns
            if (!Schema::hasColumn('results', 'behaviour_rating')) {
                $table->string('behaviour_rating')->nullable()->after('is_released');
            }
            if (!Schema::hasColumn('results', 'psychomotor_rating')) {
                $table->string('psychomotor_rating')->nullable()->after('behaviour_rating');
            }
            if (!Schema::hasColumn('results', 'affective_rating')) {
                $table->string('affective_rating')->nullable()->after('psychomotor_rating');
            }
            
            // Behaviour assessment items
            if (!Schema::hasColumn('results', 'behaviour_punctuality')) {
                $table->string('behaviour_punctuality')->nullable()->after('affective_rating');
            }
            if (!Schema::hasColumn('results', 'behaviour_participation')) {
                $table->string('behaviour_participation')->nullable()->after('behaviour_punctuality');
            }
            if (!Schema::hasColumn('results', 'behaviour_respect')) {
                $table->string('behaviour_respect')->nullable()->after('behaviour_participation');
            }
            
            // Psychomotor assessment items
            if (!Schema::hasColumn('results', 'psychomotor_handwriting')) {
                $table->string('psychomotor_handwriting')->nullable()->after('behaviour_respect');
            }
            if (!Schema::hasColumn('results', 'psychomotor_creativity')) {
                $table->string('psychomotor_creativity')->nullable()->after('psychomotor_handwriting');
            }
            if (!Schema::hasColumn('results', 'psychomotor_sports')) {
                $table->string('psychomotor_sports')->nullable()->after('psychomotor_creativity');
            }
            
            // Affective assessment items
            if (!Schema::hasColumn('results', 'affective_perseverance')) {
                $table->string('affective_perseverance')->nullable()->after('psychomotor_sports');
            }
            if (!Schema::hasColumn('results', 'affective_control')) {
                $table->string('affective_control')->nullable()->after('affective_perseverance');
            }
            if (!Schema::hasColumn('results', 'affective_initiative')) {
                $table->string('affective_initiative')->nullable()->after('affective_control');
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $columnsToDrop = [
                'teacher_comment',
                'class_teacher_comment',
                'status',
                'teacher_id',
                'approved_by',
                'submitted_at',
                'approved_at',
                'is_released',
                'behaviour_rating',
                'psychomotor_rating',
                'affective_rating',
                'behaviour_punctuality',
                'behaviour_participation',
                'behaviour_respect',
                'psychomotor_handwriting',
                'psychomotor_creativity',
                'psychomotor_sports',
                'affective_perseverance',
                'affective_control',
                'affective_initiative',
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('results', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
