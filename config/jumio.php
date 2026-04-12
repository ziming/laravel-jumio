<?php

return [
    'region' => env('JUMIO_REGION', 'amer-1'),
    'client_id' => env('JUMIO_CLIENT_ID'),
    'client_secret' => env('JUMIO_CLIENT_SECRET'),
    'user_agent' => env('JUMIO_USER_AGENT', 'laravel-jumio/1.0'),
    'callback_url' => env('JUMIO_CALLBACK_URL'),
    'workflow_definition_key' => env('JUMIO_WORKFLOW_DEFINITION_KEY'),
    'web' => [
        'success_url' => env('JUMIO_SUCCESS_URL'),
        'error_url' => env('JUMIO_ERROR_URL'),
        'locale' => env('JUMIO_LOCALE'),
    ],
    'timeout' => (int) env('JUMIO_TIMEOUT', 30),
    'connect_timeout' => (int) env('JUMIO_CONNECT_TIMEOUT', 10),
    'retry' => [
        'tries' => (int) env('JUMIO_RETRY_TRIES', 1),
        'interval_ms' => (int) env('JUMIO_RETRY_INTERVAL_MS', 250),
        'exponential_backoff' => (bool) env('JUMIO_RETRY_EXPONENTIAL_BACKOFF', false),
    ],
    'cache' => [
        'store' => env('JUMIO_CACHE_STORE'),
        'prefix' => env('JUMIO_CACHE_PREFIX', 'jumio'),
        'token_refresh_buffer' => (int) env('JUMIO_TOKEN_REFRESH_BUFFER', 300),
    ],
];
