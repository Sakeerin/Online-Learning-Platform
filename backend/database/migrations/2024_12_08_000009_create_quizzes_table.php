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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lesson_id');
            $table->string('title');
            $table->integer('passing_score')->default(80);
            $table->boolean('allow_retake')->default(true);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            
            // Unique constraint: one quiz per lesson
            $table->unique('lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};

