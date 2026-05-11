<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Valoracion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValoracionController extends Controller
{
    /**
     * Almacena una calificación recién creada en el almacenamiento.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
            'id_receptor' => 'required|exists:USUARIO,id_usuario',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id_emisor = auth()->id();

        if ($id_emisor == $request->id_receptor) {
            return response()->json(['message' => 'No puedes valorarte a ti mismo'], 400);
        }

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

    /**
     * Obtiene todas las calificaciones para un usuario específico.
     */
    public function userRatings($id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $valoraciones = Valoracion::where('id_receptor', $id)
            ->with('emisor:id_usuario,nombre')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($valoraciones);
    }
}
