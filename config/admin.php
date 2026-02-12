<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel Configuration
    |--------------------------------------------------------------------------
    */

    'token' => [
        'enabled' => env('ADMIN_TOKEN_GATE_ENABLED', true),
        'session_lifetime' => env('ADMIN_TOKEN_SESSION_LIFETIME', 1440), // minutes (24 hours)
    ],

    'routes' => [
        'token_login' => '/admin/token',
        'admin_login' => '/admin/login',
        'admin_panel' => '/admin',
    ],
];