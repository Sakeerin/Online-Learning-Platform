<?php

namespace App\Exceptions;

class PaymentFailedException extends ApiException
{
    public function __construct(string $reason = 'Payment processing failed')
    {
        parent::__construct(
            message: $reason,
            errorCode: 'PAYMENT_FAILED',
            statusCode: 402
        );
    }
}
