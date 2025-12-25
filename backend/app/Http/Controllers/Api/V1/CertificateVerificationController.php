<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;

class CertificateVerificationController extends Controller
{
    public function __construct(
        private CertificateService $certificateService
    ) {}

    /**
     * Verify a certificate by verification code (public endpoint).
     */
    public function verify(string $verificationCode): JsonResponse
    {
        $certificate = $this->certificateService->verify($verificationCode);

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found or invalid verification code.',
                'valid' => false,
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'data' => new CertificateResource($certificate),
        ]);
    }
}

