<?php


return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for API implementation
    |
    */

     /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */

    'version' => env('API_VERSION', 'v1'),

    'prefix' => env('API_PREFIX', 'api'),

    'rate_limit' => [
        'enabled' => env('API_RATE_LIMIT_ENABLED', true),
        'max_attempts' => env('API_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('API_RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    'pagination' => [
        'default_per_page' => env('API_PAGINATION_PER_PAGE', 15),
        'max_per_page' => env('API_PAGINATION_MAX_PER_PAGE', 100),
    ],

    'response' => [
        'include_trace' => env('API_INCLUDE_TRACE', false),
    ],

    'sanctum' => [
        'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 60 * 24), // 24 hours
    ],

    'track_usage' => env('API_TRACK_USAGE', false),

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        env('FRONTEND_URL', 'http://localhost:5173'),
    ],
];