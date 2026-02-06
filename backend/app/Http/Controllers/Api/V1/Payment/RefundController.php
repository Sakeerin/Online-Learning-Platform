<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Payments
 * @authenticated
 *
 * Endpoints for requesting refunds and checking refund eligibility.
 */
class RefundController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Request a refund for a transaction.
     */
    public function request(Request $request, string $transactionId): JsonResponse
    {
        $transaction = Transaction::with(['course', 'user'])
            ->findOrFail($transactionId);

        // Check if user owns the transaction
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check eligibility
        if (!$this->paymentService->isEligibleForRefund($transaction)) {
            return response()->json([
                'message' => 'Transaction is not eligible for refund. Refunds are only available within 30 days of purchase and if less than 30% of the course has been watched.',
            ], 422);
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $refund = $this->paymentService->refund(
                $transaction,
                $request->input('reason')
            );

            return response()->json([
                'message' => 'Refund processed successfully',
                'data' => new \App\Http\Resources\TransactionResource($transaction->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get refund eligibility for a transaction.
     */
    public function eligibility(Request $request, string $transactionId): JsonResponse
    {
        $transaction = Transaction::with(['course', 'user'])
            ->findOrFail($transactionId);

        // Check if user owns the transaction
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $isEligible = $this->paymentService->isEligibleForRefund($transaction);

        return response()->json([
            'eligible' => $isEligible,
            'transaction' => new \App\Http\Resources\TransactionResource($transaction),
        ]);
    }
}

