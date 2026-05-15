<?php

use Illuminate\Support\Str;

return [

    // Driver de sesión por defecto
    'driver' => env('SESSION_DRIVER', 'database'),

    // Duración de la sesión en minutos
    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    // Cerrar sesión al cerrar el navegador
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    // Cifrado de la sesión
    'encrypt' => env('SESSION_ENCRYPT', false),

    // Ruta de archivos de sesión (driver "file")
    'files' => storage_path('framework/sessions'),

    // Conexión de base de datos para sesiones
    'connection' => env('SESSION_CONNECTION'),

    // Tabla de sesiones en base de datos
    'table' => env('SESSION_TABLE', 'sessions'),

    // Almacén de caché para sesiones
    'store' => env('SESSION_STORE'),

    // Probabilidad de limpieza de sesiones antiguas (2 de cada 100)
    'lottery' => [2, 100],

    // Nombre de la cookie de sesión
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')) . '-session'
    ),

    // Ruta de la cookie
    'path' => env('SESSION_PATH', '/'),

    // Dominio de la cookie
    'domain' => env('SESSION_DOMAIN'),

    // Solo HTTPS
    'secure' => env('SESSION_SECURE_COOKIE'),

    // Solo accesible via HTTP (no JavaScript)
    'http_only' => env('SESSION_HTTP_ONLY', true),

    // Política SameSite de la cookie
    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    // Cookies particionadas
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
