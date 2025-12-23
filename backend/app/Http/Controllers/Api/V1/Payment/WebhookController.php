<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret) {
            \Log::warning('Stripe webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            \Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;

            default:
                \Log::info('Unhandled Stripe webhook event', [
                    'type' => $event->type,
                ]);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle checkout session completed event.
     */
    private function handleCheckoutSessionCompleted($session): void
    {
        try {
            if ($session->payment_status === 'paid') {
                $this->paymentService->processPayment($session->id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to process checkout session', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle payment intent succeeded event.
     */
    private function handlePaymentIntentSucceeded($paymentIntent): void
    {
        \Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    /**
     * Handle charge refunded event.
     */
    private function handleChargeRefunded($charge): void
    {
        \Log::info('Charge refunded', [
            'charge_id' => $charge->id,
        ]);
    }
}

