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
        Schema::create('lessons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('section_id');
            $table->string('title');
            $table->enum('type', ['video', 'quiz', 'article']);
            $table->json('content');
            $table->integer('duration')->nullable();
            $table->boolean('is_preview')->default(false);
            $table->integer('order');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            
            // Indexes
            $table->index(['section_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

