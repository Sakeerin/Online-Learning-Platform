<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    /**
     * Seed certificates for all completed enrollments.
     */
    public function run(): void
    {
        $completedEnrollments = Enrollment::where('is_completed', true)->get();

        foreach ($completedEnrollments as $enrollment) {
            Certificate::create([
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
                'verification_code' => Certificate::generateVerificationCode(),
                'certificate_url' => 'https://certificates.example.com/' . bin2hex(random_bytes(8)) . '.pdf',
                'issued_at' => $enrollment->completed_at ?? now(),
            ]);
        }
    }
}
