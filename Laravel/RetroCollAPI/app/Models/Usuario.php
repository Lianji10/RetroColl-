<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'USUARIO';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'fecha_registro',
        'valoracion_promedio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_vendedor', 'id_usuario');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_comprador', 'id_usuario');
    }

    public function valoracionesRecibidas()
    {
        return $this->hasMany(Valoracion::class, 'id_receptor', 'id_usuario');
    }

    public function valoracionesEnviadas()
    {
        return $this->hasMany(Valoracion::class, 'id_emisor', 'id_usuario');
    }
}
