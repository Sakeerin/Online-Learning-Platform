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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('course_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('THB');
            $table->decimal('platform_fee', 10, 2);
            $table->decimal('instructor_revenue', 10, 2);
            $table->string('payment_method', 50);
            $table->string('payment_gateway_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'refunded'])->default('pending');
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            // Indexes
            $table->index('user_id');
            $table->index('course_id');
            $table->index('payment_gateway_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

