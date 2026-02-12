<?php

namespace App\Exceptions\Api;

class UnauthorizedException extends ApiException
{
    public function __construct(
        string $message = 'Unauthorized',
        $errors = null,
        array $meta = []
    ) {
        parent::__construct($message, 401, $errors, $meta);
    }
}