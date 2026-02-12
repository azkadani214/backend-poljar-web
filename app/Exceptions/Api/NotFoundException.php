<?php

namespace App\Exceptions\Api;

class NotFoundException extends ApiException
{
    public function __construct(
        string $message = 'Resource not found',
        $errors = null,
        array $meta = []
    ) {
        parent::__construct($message, 404, $errors, $meta);
    }
}