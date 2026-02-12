<?php


namespace App\Exceptions\Api;

class ValidationException extends ApiException
{
    public function __construct(
        string $message = 'Validation failed',
        $errors = null,
        array $meta = []
    ) {
        parent::__construct($message, 422, $errors, $meta);
    }
}