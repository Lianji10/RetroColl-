<?php

use Illuminate\Support\Facades\Route;

// Servir el SPA de Vue para cualquier ruta que no sea API
Route::get('/{any}', function () {
    return file_get_contents(public_path('index.html'));
})->where('any', '^(?!api).*$');
