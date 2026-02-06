<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected string $errorCode;
    protected array $errors;
    protected int $statusCode;

    public function __construct(
        string $message = 'An error occurred',
        string $errorCode = 'ERROR',
        int $statusCode = 400,
        array $errors = []
    ) {
        parent::__construct($message, $statusCode);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
