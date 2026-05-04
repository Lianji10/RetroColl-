<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 'CERTIFICADO';
    protected $primaryKey = 'id_certificado';

    protected $fillable = ['archivo_url', 'fecha_emision', 'es_valido'];

    public function producto()
    {
        return $this->hasOne(Producto::class, 'id_certificado', 'id_certificado');
    }
}
