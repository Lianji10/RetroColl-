<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    // Verificar si el usuario es administrador
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario no es administrador, devolver error
        if (!$request->user() || $request->user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado. Se requieren permisos de administrador.'], 403);
        }

        return $next($request);
    }
}
