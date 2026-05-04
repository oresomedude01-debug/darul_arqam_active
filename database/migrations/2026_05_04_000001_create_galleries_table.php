<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create galleries table
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('cover_color', 10)->default('#3b82f6');
            $table->string('cover_icon', 50)->default('fas fa-images');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->datetime('uploaded_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->index('status');
            $table->index('uploaded_at');
        });

        // Create gallery_items table
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained('galleries')->onDelete('cascade');
            $table->string('title', 255)->nullable();
            $table->string('image_path');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->datetime('uploaded_at')->useCurrent();
            $table->timestamps();
            $table->index('gallery_id');
            $table->index('is_visible');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('galleries');
    }
};
