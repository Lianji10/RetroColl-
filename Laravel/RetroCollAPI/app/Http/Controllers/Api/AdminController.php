<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\Valoracion;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //USUARIOS

    // Listar todos los usuarios
    public function usuarios()
    {
        return response()->json(
            Usuario::select('id_usuario', 'nombre', 'email', 'rol', 'fecha_registro', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    // Cambiar el rol de un usuario
    public function cambiarRol(Request $request, $id)
    {
        // Validar campos
        $request->validate([
            'rol' => 'required|in:usuario,admin',
        ]);

        // Buscar usuario
        $usuario = Usuario::findOrFail($id);

        // No se puede cambiar el propio rol
        if ($usuario->id_usuario === $request->user()->id_usuario) {
            return response()->json(['message' => 'No puedes cambiar tu propio rol.'], 403);
        }

        $usuario->update(['rol' => $request->rol]);

        return response()->json(['message' => 'Rol actualizado correctamente.', 'usuario' => $usuario]);
    }

    // Eliminar un usuario
    public function eliminarUsuario(Request $request, $id)
    {
        // Buscar usuario
        $usuario = Usuario::findOrFail($id);

        if ($usuario->id_usuario === $request->user()->id_usuario) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta desde el panel.'], 403);
        }

        // Eliminar valoraciones (emitidas o recibidas)
        Valoracion::where('id_emisor', $usuario->id_usuario)->orWhere('id_receptor', $usuario->id_usuario)->delete();

        // Eliminar compras realizadas por el usuario
        Compra::where('id_comprador', $usuario->id_usuario)->delete();

        // Eliminar compras de los productos de este usuario
        $productosIds = Producto::where('id_vendedor', $usuario->id_usuario)->pluck('id_producto');
        Compra::whereIn('id_producto', $productosIds)->delete();

        // Eliminar sus productos primero
        Producto::where('id_vendedor', $usuario->id_usuario)->delete();

        $usuario->tokens()->delete();
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }

    // PRODUCTOS

    // Listar todos los productos
    public function productos()
    {
        return response()->json(
            Producto::with(['vendedor', 'categoria', 'plataforma'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    // Eliminar un producto
    public function eliminarProducto($id)
    {
        $producto = Producto::findOrFail($id);

        // Eliminar compras asociadas al producto
        Compra::where('id_producto', $id)->delete();

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }

    // ESTADÍSTICAS

    // Estadísticas generales del panel
    public function estadisticas()
    {
        return response()->json([
            'total_usuarios'  => Usuario::count(),
            'total_admins'    => Usuario::where('rol', 'admin')->count(),
            'total_productos' => Producto::count(),
        ]);
    }
}
