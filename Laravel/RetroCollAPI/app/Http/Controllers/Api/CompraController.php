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
                $prod = Producto::find($item['id_producto']);

                // Verificar disponibilidad
                if (!$prod || $prod->estado === 'Vendido') {
                    throw new \Exception("El producto \"" . ($prod->titulo ?? $item['id_producto']) . "\" ya no está disponible.");
                }

                // Verificar que no sea el propio vendedor
                if ($prod->id_vendedor === $usuario->id_usuario) {
                    throw new \Exception("No puedes comprar tu propio producto.");
                }

                Compra::create([
                    'id_comprador' => $usuario->id_usuario,
                    'id_producto'  => $item['id_producto'],
                    'precio_final' => $item['precio_unitario'],
                    'fecha_compra' => now(),
                ]);

                // Marcar como vendido
                $prod->estado = 'Vendido';
                $prod->save();
            }
            DB::commit();
            return response()->json(['message' => 'Compra realizada correctamente.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /** Historial de compras del usuario autenticado */
    public function misCompras(Request $request)
    {
        $compras = Compra::with(['producto.categoria', 'producto.plataforma', 'producto.vendedor'])
            ->where('id_comprador', $request->user()->id_usuario)
            ->orderBy('fecha_compra', 'desc')
            ->get();

        return response()->json($compras);
    }
}
