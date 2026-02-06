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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['course_id', 'is_completed'], 'enrollments_course_completed_idx');
            $table->index(['user_id', 'enrolled_at'], 'enrollments_user_enrolled_idx');
        });

        Schema::table('progress', function (Blueprint $table) {
            $table->index('lesson_id', 'progress_lesson_idx');
            $table->index('is_completed', 'progress_completed_idx');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['course_id', 'status', 'created_at'], 'transactions_course_status_date_idx');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('rating', 'reviews_rating_idx');
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->index(['course_id', 'created_at'], 'discussions_course_date_idx');
        });

        Schema::table('replies', function (Blueprint $table) {
            $table->index(['discussion_id', 'created_at'], 'replies_discussion_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_course_completed_idx');
            $table->dropIndex('enrollments_user_enrolled_idx');
        });

        Schema::table('progress', function (Blueprint $table) {
            $table->dropIndex('progress_lesson_idx');
            $table->dropIndex('progress_completed_idx');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_course_status_date_idx');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_rating_idx');
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->dropIndex('discussions_course_date_idx');
        });

        Schema::table('replies', function (Blueprint $table) {
            $table->dropIndex('replies_discussion_date_idx');
        });
    }
};
