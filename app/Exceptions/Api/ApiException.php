<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;

class ApiException extends Exception
{
    protected int $statusCode;
    protected $errors;
    protected array $meta;

    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 400,
        $errors = null,
        array $meta = []
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->meta = $meta;
    }

    public function render(): JsonResponse
    {
        return ResponseHelper::error(
            $this->message,
            $this->statusCode,
            $this->errors,
            $this->meta
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }
}