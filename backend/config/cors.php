<?php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',     // Vue/Vite dev server
        'http://127.0.0.1:5173',
        'https://task-frontend-972999949636.us-central1.run.app', // Your production frontend
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];