<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Mostrar listado de productos
    public function index()
    {
        return Producto::with(['vendedor', 'categoria', 'plataforma'])->get();
    }

    // Crear un nuevo producto
    public function store(Request $request)
    {
        // Validar campos
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'estado' => 'nullable|string|max:50',
            'id_categoria' => 'required|exists:CATEGORIA,id_categoria',
            'id_plataforma' => 'required|exists:PLATAFORMA,id_plataforma',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Asignar vendedor y fecha
        $validated['id_vendedor'] = $request->user()->id_usuario;
        $validated['fecha_publicacion'] = now();

        // Guardar imagen si se ha subido
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $producto = Producto::create($validated);

        return response()->json($producto, 201);
    }

    // Mostrar un producto
    public function show($id)
    {
        $producto = Producto::with(['vendedor', 'categoria', 'plataforma'])->find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return $producto;
    }

    // Actualizar un producto
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Verificar que sea el propietario o admin
        if ($producto->id_vendedor !== $request->user()->id_usuario && $request->user()->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Validar campos
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'sometimes|numeric',
            'estado' => 'nullable|string|max:50',
            'id_categoria' => 'sometimes|exists:CATEGORIA,id_categoria',
            'id_plataforma' => 'sometimes|exists:PLATAFORMA,id_plataforma',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Actualizar imagen si se ha subido
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $producto->update($validated);

        return response()->json($producto);
    }

    // Eliminar un producto
    public function destroy(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Verificar que sea el propietario
        if ($producto->id_vendedor !== $request->user()->id_usuario) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
