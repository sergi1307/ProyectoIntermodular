<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://polite-sky-04db4581e.1.azurestaticapps.net', 
        'http://localhost:5173', 
        'http://localhost:5174',
        'http://127.0.0.1:5173' 
    ],

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,

    'supports_credentials' => true, 
];