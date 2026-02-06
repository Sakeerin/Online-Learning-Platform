<?php

namespace App\Exceptions;

class NotEnrolledException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            message: 'You must be enrolled in this course to perform this action.',
            errorCode: 'NOT_ENROLLED',
            statusCode: 403
        );
    }
}
