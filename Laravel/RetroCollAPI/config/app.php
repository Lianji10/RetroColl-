<?php

return [

    // Nombre de la aplicación
    'name' => env('APP_NAME', 'Laravel'),

    // Entorno de la aplicación (local, production...)
    'env' => env('APP_ENV', 'production'),

    // Modo depuración (false en producción)
    'debug' => (bool) env('APP_DEBUG', false),

    // URL base de la aplicación
    'url' => env('APP_URL', 'http://localhost'),

    // Zona horaria
    'timezone' => 'UTC',

    // Idioma de la aplicación
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    // Clave de cifrado y algoritmo
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    // Modo de mantenimiento
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store'  => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
