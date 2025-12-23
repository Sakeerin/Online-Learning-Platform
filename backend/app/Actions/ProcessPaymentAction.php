<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Services\PaymentService;

class ProcessPaymentAction
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Process a payment from Stripe checkout session.
     */
    public function execute(string $sessionId): Transaction
    {
        return $this->paymentService->processPayment($sessionId);
    }
}

