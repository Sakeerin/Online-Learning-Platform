<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\Enrollment;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateService $certificateService
    ) {}

    /**
     * Get all certificates for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $certificates = $this->certificateService->getUserCertificates(Auth::id());

        return response()->json([
            'data' => CertificateResource::collection($certificates),
        ]);
    }

    /**
     * Get certificate for a specific enrollment.
     */
    public function show(string $enrollmentId): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);

        // Verify user owns the enrollment
        if ($enrollment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $certificate = $this->certificateService->getEnrollmentCertificate($enrollment->id);

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found. Course must be completed to receive a certificate.',
            ], 404);
        }

        return response()->json([
            'data' => new CertificateResource($certificate),
        ]);
    }

    /**
     * Download certificate PDF.
     */
    public function download(string $certificateId): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $certificate = \App\Models\Certificate::findOrFail($certificateId);

        // Verify user owns the certificate
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$certificate->certificate_url) {
            return response()->json([
                'message' => 'Certificate PDF is still being generated. Please try again later.',
            ], 202);
        }

        // Redirect to certificate URL (S3 or local storage)
        return redirect($certificate->certificate_url);
    }
}

