<?php

namespace App\Listeners;

use App\Actions\GenerateCertificateAction;
use App\Events\CourseCompleted;
use App\Mail\CertificateIssuedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

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
        $certificate = $this->generateCertificateAction->execute($event->enrollment);

        // Send certificate issued email to the student
        $student = $event->enrollment->student;
        Mail::to($student)->send(new CertificateIssuedMail($certificate));
    }
}

