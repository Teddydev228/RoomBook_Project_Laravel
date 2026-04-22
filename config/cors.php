<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | CORS headers allow cross-origin requests. Configured for development
    | with backend APIs running on different ports (8001+).
    |
    */

    'paths' => ['api/*', '-sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Allow all origins for development
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
