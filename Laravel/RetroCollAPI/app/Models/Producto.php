<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'PRODUCTO';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'titulo',
        'descripcion',
        'precio',
        'estado',
        'fecha_publicacion',
        'id_vendedor',
        'id_categoria',
        'id_plataforma',
        'id_certificado',
        'imagen',
    ];

    public function vendedor()
    {
        return $this->belongsTo(Usuario::class, 'id_vendedor', 'id_usuario');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function plataforma()
    {
        return $this->belongsTo(Plataforma::class, 'id_plataforma', 'id_plataforma');
    }

    public function certificado()
    {
        return $this->belongsTo(Certificado::class, 'id_certificado', 'id_certificado');
    }

    public function compra()
    {
        return $this->hasOne(Compra::class, 'id_producto', 'id_producto');
    }
}
