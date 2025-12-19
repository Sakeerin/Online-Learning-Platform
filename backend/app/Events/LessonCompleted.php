<?php

namespace App\Events;

use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Enrollment $enrollment,
        public Lesson $lesson
    ) {}
}

