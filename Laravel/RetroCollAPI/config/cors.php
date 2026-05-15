<?php

// Configuración de CORS (Cross-Origin Resource Sharing)
// Permite que el frontend Vue pueda consumir la API desde distintos orígenes

return [

    // Rutas afectadas por CORS
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Orígenes permitidos (local y producción)
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://retrocoll.giancweb.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Necesario para enviar cookies con las peticiones
    'supports_credentials' => true,

];
