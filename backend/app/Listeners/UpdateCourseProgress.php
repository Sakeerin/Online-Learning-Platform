<?php

namespace App\Listeners;

use App\Actions\CalculateProgressAction;
use App\Events\LessonCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCourseProgress implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private CalculateProgressAction $calculateProgressAction
    ) {}

    /**
     * Handle the event.
     */
    public function handle(LessonCompleted $event): void
    {
        // Recalculate enrollment progress when a lesson is completed
        $this->calculateProgressAction->execute($event->enrollment);
    }
}

