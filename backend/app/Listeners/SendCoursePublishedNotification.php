<?php

namespace App\Listeners;

use App\Events\CoursePublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendCoursePublishedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CoursePublished $event): void
    {
        $course = $event->course;
        $instructor = $course->instructor;

        Log::info('Course published notification', [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'instructor_id' => $instructor->id,
            'instructor_email' => $instructor->email,
        ]);

        // TODO: Send email notification to instructor
        // Notification::send($instructor, new CoursePublishedNotification($course));
    }
}

