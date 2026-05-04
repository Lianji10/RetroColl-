<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'COMPRA';
    protected $primaryKey = 'id_compra';

    protected $fillable = [
        'fecha_compra',
        'precio_final',
        'id_comprador',
        'id_producto',
    ];

    public function comprador()
    {
        return $this->belongsTo(Usuario::class, 'id_comprador', 'id_usuario');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
