<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Categorías (Géneros de videojuegos)
        DB::table('CATEGORIA')->insert([
            ['nombre' => 'Plataformas'],
            ['nombre' => 'RPG'],
            ['nombre' => 'Acción'],
            ['nombre' => 'Aventura'],
            ['nombre' => 'Deportes'],
            ['nombre' => 'Carreras'],
        ]);

        // Plataformas
        DB::table('PLATAFORMA')->insert([
            ['nombre' => 'NES'],
            ['nombre' => 'SNES'],
            ['nombre' => 'Nintendo 64'],
            ['nombre' => 'GameCube'],
            ['nombre' => 'Game Boy'],
            ['nombre' => 'Mega Drive'],
            ['nombre' => 'PlayStation 1'],
            ['nombre' => 'PlayStation 2'],
            ['nombre' => 'Dreamcast'],
        ]);
    }
}
