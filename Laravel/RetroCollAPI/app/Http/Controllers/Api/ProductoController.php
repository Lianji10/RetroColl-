<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar listado de productos.
     */
    public function index()
    {
        return Producto::with(['vendedor', 'categoria', 'plataforma'])->get();
    }

    /**
     * Almacenar un nuevo producto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'estado' => 'nullable|string|max:50',
            'id_categoria' => 'required|exists:CATEGORIA,id_categoria',
            'id_plataforma' => 'required|exists:PLATAFORMA,id_plataforma',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validated['id_vendedor'] = $request->user()->id_usuario;
        $validated['fecha_publicacion'] = now();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $producto = Producto::create($validated);

        return response()->json($producto, 201);
    }

    /**
     * Mostrar un producto específico.
     */
    public function show($id)
    {
        $producto = Producto::with(['vendedor', 'categoria', 'plataforma'])->find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return $producto;
    }

    /**
     * Actualizar un producto.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        if ($producto->id_vendedor !== $request->user()->id_usuario && $request->user()->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'sometimes|numeric',
            'estado' => 'nullable|string|max:50',
            'id_categoria' => 'sometimes|exists:CATEGORIA,id_categoria',
            'id_plataforma' => 'sometimes|exists:PLATAFORMA,id_plataforma',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $producto->update($validated);

        return response()->json($producto);
    }

    /**
     * Eliminar un producto.
     */
    public function destroy(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        if ($producto->id_vendedor !== $request->user()->id_usuario) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
