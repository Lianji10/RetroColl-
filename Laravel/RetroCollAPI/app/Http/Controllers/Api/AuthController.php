<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Registro de usuarios
    public function registrar(Request $request)
    {
        // Validar campos
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:USUARIO',
            'password' => 'required|string|min:8',
        ]);

        // Crear usuario
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'fecha_registro' => now(),
        ]);

        // Crear token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario,
        ]);
    }

    // Login de usuarios
    public function login(Request $request)
    {
        // Validar campos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar usuario
        $usuario = Usuario::where('email', $request->email)->first();

        // Verificar contraseña
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Crear token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario,
        ]);
    }

    // Logout de usuarios
    public function logout(Request $request)
    {
        // Eliminar token de sesion
        $request->user()->tokens()->delete();
        
        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    // Actualizar perfil de usuario
    public function actualizarPerfil(Request $request)
    {
        $usuario = $request->user();

        // Validar campos
        $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|string|email|max:100|unique:USUARIO,email,' . $usuario->id_usuario . ',id_usuario',
            'password_actual' => 'required_with:password|string',
            'password' => 'sometimes|required|string|min:8',
        ]);

        if ($request->filled('password')) {
            // Verificar contraseña actual
            if (!Hash::check($request->password_actual, $usuario->password)) {
                throw ValidationException::withMessages([
                    'password_actual' => ['La contraseña actual es incorrecta.'],
                ]);
            }
            // Actualizar contraseña
            $usuario->password = Hash::make($request->password);
        }

        if ($request->has('nombre')) {
            $usuario->nombre = $request->nombre;
        }

        if ($request->has('email')) {
            $usuario->email = $request->email;
        }

        $usuario->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'usuario' => $usuario,
        ]);
    }
}
