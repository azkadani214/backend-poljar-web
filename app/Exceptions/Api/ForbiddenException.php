<?php

namespace App\Exceptions\Api;

class ForbiddenException extends ApiException
{
    public function __construct(
        string $message = 'Forbidden',
        $errors = null,
        array $meta = []
    ) {
        parent::__construct($message, 403, $errors, $meta);
    }
}