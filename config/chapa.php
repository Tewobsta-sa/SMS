<?php

return [
    'secret_key' => env('CHAPA_SECRET_KEY', ''),
    'public_key' => env('CHAPA_PUBLIC_KEY', ''),
    'timeout'    => env('CHAPA_HTTP_TIMEOUT', 10),
    'base_url'   => env('CHAPA_BASE_URL', 'https://api.chapa.co'),
    // Frontend return URL base
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
];
