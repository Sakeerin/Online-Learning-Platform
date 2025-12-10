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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('course_id');
            $table->integer('rating');
            $table->text('review_text')->nullable();
            $table->integer('helpful_votes')->default(0);
            $table->text('instructor_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            // Unique constraint: one review per user per course
            $table->unique(['user_id', 'course_id']);
            
            // Check constraint for rating (1-5)
            $table->check('rating >= 1 AND rating <= 5');
            
            // Indexes
            $table->index('course_id');
            $table->index('created_at');
            $table->index('is_flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

