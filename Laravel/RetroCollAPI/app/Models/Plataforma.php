<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plataforma extends Model
{
    protected $table = 'PLATAFORMA';
    protected $primaryKey = 'id_plataforma';

    protected $fillable = ['nombre'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_plataforma', 'id_plataforma');
    }
}
