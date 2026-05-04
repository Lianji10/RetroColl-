<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id_producto' => 'required|exists:PRODUCTO,id_producto',
            'items.*.precio_unitario' => 'required|numeric',
        ]);

        $usuario = $request->user();

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                // Verificar si ya existe en COMPRA (es un artículo único)
                $existente = Compra::where('id_producto', $item['id_producto'])->first();
                if ($existente) {
                    throw new \Exception("El producto " . $item['id_producto'] . " ya fue vendido.");
                }

                Compra::create([
                    'id_comprador' => $usuario->id_usuario,
                    'id_producto' => $item['id_producto'],
                    'precio_final' => $item['precio_unitario'],
                    'fecha_compra' => now(),
                ]);

                // Opcional: Actualizar el producto
                $prod = Producto::find($item['id_producto']);
                if ($prod) {
                    $prod->estado = 'Vendido';
                    $prod->save();
                }
            }
            DB::commit();
            return response()->json(['message' => 'Compra realizada correctamente.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al tramitar compra', 'error' => $e->getMessage()], 400);
        }
    }
}
