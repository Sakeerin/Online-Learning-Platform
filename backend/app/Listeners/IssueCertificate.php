<?php

namespace App\Listeners;

use App\Actions\GenerateCertificateAction;
use App\Events\CourseCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IssueCertificate implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private GenerateCertificateAction $generateCertificateAction
    ) {}

    /**
     * Handle the event.
     */
    public function handle(CourseCompleted $event): void
    {
        // Generate certificate when course is completed
        $this->generateCertificateAction->execute($event->enrollment);
    }
}

