<?php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateInstructorRevenue implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentProcessed $event): void
    {
        $transaction = $event->transaction;
        $course = $transaction->course;

        // Update instructor revenue tracking
        // This could be stored in a separate instructor_revenue table
        // or calculated on-the-fly. For now, we'll just log it.
        // In a production system, you might want to:
        // 1. Create an instructor_revenue table
        // 2. Update instructor balance
        // 3. Track payout schedules
        
        \Log::info('Instructor revenue updated', [
            'instructor_id' => $course->instructor_id,
            'course_id' => $course->id,
            'revenue' => $transaction->instructor_revenue,
            'transaction_id' => $transaction->id,
        ]);
    }
}

