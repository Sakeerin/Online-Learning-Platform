<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate PDF certificate and upload to S3.
     * 
     * Note: Requires PDF library (e.g., barryvdh/laravel-dompdf)
     * Install with: composer require barryvdh/laravel-dompdf
     */
    public function generatePdf(Certificate $certificate): string
    {
        $certificate->load(['user', 'course', 'enrollment']);

        // Generate PDF HTML content
        $html = $this->generateCertificateHtml($certificate);

        // For now, save HTML as placeholder
        // TODO: Install PDF library and generate actual PDF
        // Example with dompdf:
        // $pdf = \PDF::loadHTML($html);
        // $pdfContent = $pdf->output();
        
        // For now, we'll create a simple HTML file that can be converted to PDF
        $filename = "certificates/{$certificate->id}/certificate.html";
        Storage::disk('public')->put($filename, $html);

        // In production, generate PDF and upload to S3:
        // $pdfPath = "certificates/{$certificate->id}/certificate.pdf";
        // Storage::disk('s3')->put($pdfPath, $pdfContent, 'public');
        // return Storage::disk('s3')->url($pdfPath);

        // Temporary: return local URL
        return Storage::disk('public')->url($filename);
    }

    /**
     * Generate HTML content for certificate.
     */
    private function generateCertificateHtml(Certificate $certificate): string
    {
        $user = $certificate->user;
        $course = $certificate->course;
        $issuedDate = $certificate->issued_at->format('F j, Y');
        $verificationCode = $certificate->verification_code;

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 40px;
            background: #f5f5f5;
        }
        .certificate {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 60px;
            border: 10px solid #1a365d;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 48px;
            color: #1a365d;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .header p {
            font-size: 18px;
            color: #666;
            margin: 10px 0;
        }
        .content {
            text-align: center;
            margin: 40px 0;
        }
        .content p {
            font-size: 20px;
            line-height: 1.8;
            margin: 20px 0;
        }
        .student-name {
            font-size: 36px;
            font-weight: bold;
            color: #1a365d;
            margin: 30px 0;
            text-decoration: underline;
        }
        .course-name {
            font-size: 28px;
            color: #2d3748;
            margin: 20px 0;
            font-style: italic;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .verification {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            font-size: 12px;
            color: #718096;
        }
        .verification-code {
            font-family: monospace;
            font-size: 14px;
            font-weight: bold;
            color: #1a365d;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <h1>Certificate of Completion</h1>
            <p>This is to certify that</p>
        </div>
        <div class="content">
            <p class="student-name">{$user->name}</p>
            <p>has successfully completed the course</p>
            <p class="course-name">{$course->title}</p>
            <p>on {$issuedDate}</p>
        </div>
        <div class="footer">
            <div class="verification">
                <p>Verification Code:</p>
                <p class="verification-code">{$verificationCode}</p>
                <p style="margin-top: 10px;">Verify this certificate at: {url('/certificates/verify/' . $verificationCode)}</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Verify a certificate by verification code.
     */
    public function verify(string $verificationCode): ?Certificate
    {
        return Certificate::where('verification_code', $verificationCode)
            ->with(['user', 'course'])
            ->first();
    }

    /**
     * Get all certificates for a user.
     */
    public function getUserCertificates(string $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Certificate::where('user_id', $userId)
            ->with(['course'])
            ->orderBy('issued_at', 'desc')
            ->get();
    }

    /**
     * Get certificate for a specific enrollment.
     */
    public function getEnrollmentCertificate(string $enrollmentId): ?Certificate
    {
        return Certificate::where('enrollment_id', $enrollmentId)
            ->with(['user', 'course'])
            ->first();
    }
}

