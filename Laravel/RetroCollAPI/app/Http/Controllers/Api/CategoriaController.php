<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Plataforma;

class CategoriaController extends Controller
{
    public function index()
    {
        return Categoria::all();
    }

    public function plataformas()
    {
        return Plataforma::all();
    }
}
