<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Course;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Payments
 * @authenticated
 *
 * Endpoints for creating checkout sessions and verifying payment status.
 */
class CheckoutController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Create a checkout session for course purchase.
     */
    public function create(CheckoutRequest $request): JsonResponse
    {
        $course = Course::findOrFail($request->course_id);
        $user = $request->user();

        try {
            // Build success and cancel URLs
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $successUrl = $frontendUrl . '/payment/success';
            $cancelUrl = $frontendUrl . '/courses/' . $course->id;

            $session = $this->paymentService->createCheckout(
                $user,
                $course,
                $successUrl,
                $cancelUrl
            );

            return response()->json([
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Verify payment status after redirect from Stripe.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        try {
            $transaction = $this->paymentService->processPayment($request->session_id);

            return response()->json([
                'message' => 'Payment successful',
                'data' => new \App\Http\Resources\TransactionResource($transaction->load(['course', 'user'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

