<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de CORS (Cross-Origin Resource Sharing)
    |--------------------------------------------------------------------------
    |
    | Aquí puede configurar los ajustes para el intercambio de recursos entre
    | orígenes. Como el frontend y la API se sirven desde el mismo dominio
    | en producción, se permiten todos los orígenes necesarios.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://retrocoll.giancweb.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
