<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Valoracion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValoracionController extends Controller
{
    // Crear una valoracion
    public function store(Request $request)
    {
        // Validar campos
        $validator = Validator::make($request->all(), [
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
            'id_receptor' => 'required|exists:USUARIO,id_usuario',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Obtener usuario autenticado
        $id_emisor = auth()->id();

        if ($id_emisor == $request->id_receptor) {
            return response()->json(['message' => 'No puedes valorarte a ti mismo'], 400);
        }

        // Guardar valoracion
        $valoracion = Valoracion::create([
            'puntuacion' => $request->puntuacion,
            'comentario' => $request->comentario,
            'fecha' => now(),
            'id_emisor' => $id_emisor,
            'id_receptor' => $request->id_receptor,
        ]);

        return response()->json([
            'message' => 'Valoración creada con éxito',
            'valoracion' => $valoracion
        ], 201);
    }

    // Obtener valoraciones de un usuario
    public function userRatings($id)
    {
        // Buscar usuario
        $usuario = Usuario::findOrFail($id);

        // Obtener valoraciones ordenadas por fecha
        $valoraciones = Valoracion::where('id_receptor', $id)
            ->with('emisor:id_usuario,nombre')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($valoraciones);
    }
}
