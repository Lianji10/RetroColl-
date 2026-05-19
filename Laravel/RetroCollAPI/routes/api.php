<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ValoracionController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas 
Route::post('/registrar', [AuthController::class, 'registrar'])->middleware('throttle:10,1');
Route::post('/login',     [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::get('/productos',      [ProductoController::class, 'index']);
Route::get('/productos/mis-productos', [ProductoController::class, 'misProductos'])->middleware('auth:sanctum');
Route::get('/productos/{id}', [ProductoController::class, 'show']);

Route::get('/categorias',  [CategoriaController::class, 'index']);
Route::get('/plataformas', [CategoriaController::class, 'plataformas']);
Route::get('/usuarios/{id}/valoraciones', [ValoracionController::class, 'userRatings']);

// Rutas Protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Illuminate\Http\Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil']);

    Route::post('/productos',      [ProductoController::class, 'store']);
    Route::put('/productos/{id}',  [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

    // Compras
    Route::post('/compras',             [CompraController::class, 'store']);
    Route::get('/compras/mis-compras',  [CompraController::class, 'misCompras']);

    // Valoraciones
    Route::post('/valoraciones', [ValoracionController::class, 'store']);

    // Rutas de Administrador
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/estadisticas', [AdminController::class, 'estadisticas']);

        Route::get('/usuarios',              [AdminController::class, 'usuarios']);
        Route::put('/usuarios/{id}/rol',     [AdminController::class, 'cambiarRol']);
        Route::delete('/usuarios/{id}',      [AdminController::class, 'eliminarUsuario']);

        Route::get('/productos',             [AdminController::class, 'productos']);
        Route::delete('/productos/{id}',     [AdminController::class, 'eliminarProducto']);
    });
});
