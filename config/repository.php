<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Repository Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for repository pattern implementation
    |
    */

    'cache' => [
        'enabled' => env('REPOSITORY_CACHE_ENABLED', false),
        'minutes' => env('REPOSITORY_CACHE_MINUTES', 60),
    ],

    'pagination' => [
        'default_per_page' => env('REPOSITORY_PAGINATION_PER_PAGE', 15),
        'max_per_page' => env('REPOSITORY_PAGINATION_MAX_PER_PAGE', 100),
    ],
];