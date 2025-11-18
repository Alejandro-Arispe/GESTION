<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        // Desarrollo local
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:8000',
        'http://localhost:8000',
        // Producción en Render
        env('APP_URL', 'http://localhost'),
    ],
    'allowed_origins_patterns' => [
        // Permitir cualquier subdominio de render.com
        '#^https?://.*\.onrender\.com$#',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];