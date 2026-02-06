<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed transactions for all paid course enrollments.
     * Status: completed. Platform fee: 30%.
     */
    public function run(): void
    {
        // Get all enrollments where the course has a price > 0
        $enrollments = Enrollment::with('course')->get();

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;

            // Only create transactions for paid courses
            if ($course && $course->price > 0) {
                $amount = (float) $course->price;
                $platformFee = round($amount * 0.30, 2);
                $instructorRevenue = round($amount - $platformFee, 2);

                Transaction::create([
                    'user_id' => $enrollment->user_id,
                    'course_id' => $course->id,
                    'amount' => $amount,
                    'currency' => 'THB',
                    'platform_fee' => $platformFee,
                    'instructor_revenue' => $instructorRevenue,
                    'payment_method' => fake()->randomElement(['stripe', 'stripe', 'paypal']),
                    'payment_gateway_id' => 'ch_' . fake()->unique()->regexify('[a-zA-Z0-9]{24}'),
                    'status' => 'completed',
                    'refunded_at' => null,
                    'refund_reason' => null,
                ]);
            }
        }
    }
}
