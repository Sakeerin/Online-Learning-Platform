<?php

namespace App\Listeners;

use App\Events\StudentEnrolled;
use App\Mail\EnrollmentConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEnrollmentConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(StudentEnrolled $event): void
    {
        $enrollment = $event->enrollment;
        $student = $enrollment->student;
        $course = $enrollment->course;

        Log::info('Student enrolled confirmation', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'course_title' => $course->title,
        ]);

        Mail::to($student)->send(new EnrollmentConfirmationMail($enrollment));
    }
}

