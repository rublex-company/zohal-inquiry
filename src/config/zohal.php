<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zohal API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Zohal inquiry service API
    |
    */

    'base_url' => env('ZOHAL_BASE_URL', 'https://service.zohal.io/api/v0/services'),
    'token' => env('ZOHAL_TOKEN'),
    'timeout' => env('ZOHAL_TIMEOUT', 30),
    'retry_attempts' => env('ZOHAL_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('ZOHAL_RETRY_DELAY', 1000),
];
