<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    protected $table = 'VALORACION';
    protected $primaryKey = 'id_valoracion';

    protected $fillable = [
        'puntuacion',
        'comentario',
        'fecha',
        'id_emisor',
        'id_receptor',
    ];

    public function emisor()
    {
        return $this->belongsTo(Usuario::class, 'id_emisor', 'id_usuario');
    }

    public function receptor()
    {
        return $this->belongsTo(Usuario::class, 'id_receptor', 'id_usuario');
    }
}
