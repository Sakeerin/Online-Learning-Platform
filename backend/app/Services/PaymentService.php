<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Refund;
use Stripe\Stripe;

class PaymentService
{
    private const PLATFORM_FEE_RATE = 0.30; // 30% platform fee

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout session for course purchase.
     */
    public function createCheckout(User $user, Course $course, string $successUrl, string $cancelUrl): Session
    {
        // Check if course is published
        if (!$course->isPublished()) {
            throw new \Exception('Cannot purchase unpublished course.');
        }

        // Check if course is free
        if ($course->price == 0) {
            throw new \Exception('Cannot create checkout for free course.');
        }

        // Check if user already enrolled
        $existingEnrollment = \App\Models\Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($existingEnrollment) {
            throw new \Exception('You are already enrolled in this course.');
        }

        // Calculate fees
        $amount = $course->price;
        $platformFee = $amount * self::PLATFORM_FEE_RATE;
        $instructorRevenue = $amount - $platformFee;

        // Create transaction record
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $amount,
            'currency' => $course->currency ?? 'THB',
            'platform_fee' => $platformFee,
            'instructor_revenue' => $instructorRevenue,
            'payment_method' => 'stripe',
            'status' => 'pending',
        ]);

        try {
            // Create Stripe Checkout Session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($course->currency ?? 'thb'),
                        'product_data' => [
                            'name' => $course->title,
                            'description' => $course->subtitle ?? substr($course->description, 0, 200),
                        ],
                        'unit_amount' => (int)($amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $transaction->id,
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ],
            ]);

            // Update transaction with Stripe session ID
            $transaction->update([
                'payment_gateway_id' => $session->id,
            ]);

            return $session;
        } catch (ApiErrorException $e) {
            // Update transaction status on error
            $transaction->update(['status' => 'failed']);
            throw new \Exception('Failed to create checkout session: ' . $e->getMessage());
        }
    }

    /**
     * Process payment completion from Stripe webhook.
     */
    public function processPayment(string $sessionId): Transaction
    {
        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                throw new \Exception('Payment not completed.');
            }

            $transaction = Transaction::where('payment_gateway_id', $sessionId)
                ->orWhere('id', $session->client_reference_id)
                ->firstOrFail();

            if ($transaction->isCompleted()) {
                return $transaction; // Already processed
            }

            return DB::transaction(function () use ($transaction, $session) {
                // Update transaction status
                $transaction->update([
                    'status' => 'completed',
                    'payment_gateway_id' => $session->id,
                ]);

                // Enroll student in course
                $enrollmentService = app(EnrollmentService::class);
                $enrollment = $enrollmentService->enroll(
                    $transaction->user,
                    $transaction->course
                );

                // Dispatch payment processed event
                event(new \App\Events\PaymentProcessed($transaction));

                return $transaction->fresh();
            });
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to process payment: ' . $e->getMessage());
        }
    }

    /**
     * Process refund for a transaction.
     */
    public function refund(Transaction $transaction, ?string $reason = null): Refund
    {
        if (!$transaction->isCompleted()) {
            throw new \Exception('Can only refund completed transactions.');
        }

        if ($transaction->isRefunded()) {
            throw new \Exception('Transaction already refunded.');
        }

        if (!$transaction->isEligibleForRefund()) {
            throw new \Exception('Transaction is not eligible for refund.');
        }

        try {
            // Create refund in Stripe
            $refund = \Stripe\Refund::create([
                'payment_intent' => $this->getPaymentIntentFromSession($transaction->payment_gateway_id),
                'amount' => (int)($transaction->amount * 100), // Convert to cents
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'refund_reason' => $reason ?? 'Customer request',
                ],
            ]);

            // Update transaction
            $transaction->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'refund_reason' => $reason,
            ]);

            return $refund;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to process refund: ' . $e->getMessage());
        }
    }

    /**
     * Get payment intent ID from checkout session.
     */
    private function getPaymentIntentFromSession(string $sessionId): string
    {
        try {
            $session = Session::retrieve($sessionId);
            
            // payment_intent can be a string ID or an object
            $paymentIntentId = is_string($session->payment_intent) 
                ? $session->payment_intent 
                : $session->payment_intent->id;
            
            if (!$paymentIntentId) {
                throw new \Exception('Payment intent not found in session.');
            }
            
            return $paymentIntentId;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to retrieve payment intent: ' . $e->getMessage());
        }
    }

    /**
     * Check if transaction is eligible for refund.
     */
    public function isEligibleForRefund(Transaction $transaction): bool
    {
        return $transaction->isEligibleForRefund();
    }
}

