<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateCourseCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Certificate $certificate
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CertificateService $certificateService): void
    {
        try {
            Log::info('Generating certificate PDF', [
                'certificate_id' => $this->certificate->id,
                'verification_code' => $this->certificate->verification_code,
            ]);

            // Generate PDF and upload to S3
            $certificateUrl = $certificateService->generatePdf($this->certificate);

            // Update certificate with PDF URL
            $this->certificate->update([
                'certificate_url' => $certificateUrl,
            ]);

            Log::info('Certificate PDF generated successfully', [
                'certificate_id' => $this->certificate->id,
                'certificate_url' => $certificateUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate certificate PDF', [
                'certificate_id' => $this->certificate->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

