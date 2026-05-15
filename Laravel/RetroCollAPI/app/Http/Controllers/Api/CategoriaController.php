<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Plataforma;

class CategoriaController extends Controller
{
    // Listado de categorías
    public function index()
    {
        return Categoria::all();
    }

    // Listado de plataformas
    public function plataformas()
    {
        return Plataforma::all();
    }
}
