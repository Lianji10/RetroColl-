<?php

return [

    // Guard y broker de autenticación por defecto
    'defaults' => [
        'guard'     => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    // Guardias de autenticación
    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
    ],

    // Proveedores de usuario (Eloquent con el modelo Usuario)
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => env('AUTH_MODEL', App\Models\Usuario::class),
        ],
    ],

    // Configuración para el restablecimiento de contraseñas
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    // Tiempo de espera para confirmación de contraseña (segundos)
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
