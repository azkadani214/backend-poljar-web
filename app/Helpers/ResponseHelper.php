<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResponseHelper
{
    /**
     * Success response
     */
    public static function success(
        $data = null,
        string $message = 'Success',
        int $code = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'Error',
        int $code = 400,
        $errors = null,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Paginated response
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        $resourceClass = null,
        string $message = 'Success',
        array $meta = []
    ): JsonResponse {
        $data = $resourceClass 
            ? $resourceClass::collection($paginator->items())
            : $paginator->items();

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, 200);
    }

    /**
     * Created response
     */
    public static function created(
        $data = null,
        string $message = 'Resource created successfully',
        array $meta = []
    ): JsonResponse {
        return self::success($data, $message, 201, $meta);
    }

    /**
     * Updated response
     */
    public static function updated(
        $data = null,
        string $message = 'Resource updated successfully',
        array $meta = []
    ): JsonResponse {
        return self::success($data, $message, 200, $meta);
    }

    /**
     * Deleted response
     */
    public static function deleted(
        string $message = 'Resource deleted successfully',
        array $meta = []
    ): JsonResponse {
        return self::success(null, $message, 200, $meta);
    }

    /**
     * Not found response
     */
    public static function notFound(
        string $message = 'Resource not found',
        array $meta = []
    ): JsonResponse {
        return self::error($message, 404, null, $meta);
    }

    /**
     * Validation error response
     */
    public static function validationError(
        $errors,
        string $message = 'Validation failed',
        array $meta = []
    ): JsonResponse {
        return self::error($message, 422, $errors, $meta);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized(
        string $message = 'Unauthorized',
        array $meta = []
    ): JsonResponse {
        return self::error($message, 401, null, $meta);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(
        string $message = 'Forbidden',
        array $meta = []
    ): JsonResponse {
        return self::error($message, 403, null, $meta);
    }

    /**
     * Server error response
     */
    public static function serverError(
        string $message = 'Internal server error',
        array $meta = []
    ): JsonResponse {
        return self::error($message, 500, null, $meta);
    }

    /**
     * No content response
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}