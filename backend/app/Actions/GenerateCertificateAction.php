<?php

namespace App\Actions;

use App\Jobs\GenerateCourseCertificate;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class GenerateCertificateAction
{
    /**
     * Generate a certificate for a completed enrollment.
     */
    public function execute(Enrollment $enrollment): Certificate
    {
        return DB::transaction(function () use ($enrollment) {
            // Check if certificate already exists
            $existingCertificate = Certificate::where('enrollment_id', $enrollment->id)->first();
            if ($existingCertificate) {
                return $existingCertificate;
            }

            // Verify enrollment is completed
            if (!$enrollment->is_completed) {
                throw new \Exception('Cannot generate certificate for incomplete enrollment.');
            }

            // Create certificate record
            $certificate = Certificate::create([
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
                'verification_code' => Certificate::generateVerificationCode(),
                'issued_at' => now(),
            ]);

            // Dispatch job to generate PDF
            GenerateCourseCertificate::dispatch($certificate);

            return $certificate->fresh();
        });
    }
}

