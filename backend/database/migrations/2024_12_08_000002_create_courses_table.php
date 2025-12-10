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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('instructor_id');
            $table->string('title');
            $table->string('subtitle', 500)->nullable();
            $table->text('description');
            $table->json('learning_objectives')->nullable();
            $table->string('category', 100);
            $table->string('subcategory', 100)->nullable();
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']);
            $table->string('thumbnail')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('THB');
            $table->enum('status', ['draft', 'published', 'unpublished'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('total_enrollments')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('instructor_id');
            $table->index('category');
            $table->index('status');
            $table->index('average_rating');
            $table->index('created_at');
            
            // Full-text search index (PostgreSQL)
            if (config('database.default') === 'pgsql') {
                $table->rawIndex("to_tsvector('english', title || ' ' || description)", 'courses_search_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

