<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // Ejecucion de seeders
    public function run(): void
    {
        // Categorías (Géneros de videojuegos)
        DB::table('CATEGORIA')->insertOrIgnore([
            ['nombre' => 'Plataformas'],
            ['nombre' => 'RPG'],
            ['nombre' => 'Acción'],
            ['nombre' => 'Aventura'],
            ['nombre' => 'Deportes'],
            ['nombre' => 'Carreras'],
            ['nombre' => 'Lucha'],
            ['nombre' => 'Disparos'],
            ['nombre' => 'Estrategia'],
            ['nombre' => 'Simulación'],
            ['nombre' => 'Puzzle'],
            ['nombre' => 'Terror'],
        ]);

        // Plataformas (consolas)
        DB::table('PLATAFORMA')->insertOrIgnore([
            // Nintendo
            ['nombre' => 'NES'],
            ['nombre' => 'SNES'],
            ['nombre' => 'Nintendo 64'],
            ['nombre' => 'GameCube'],
            ['nombre' => 'Wii'],
            ['nombre' => 'Game Boy'],
            ['nombre' => 'Game Boy Color'],
            ['nombre' => 'Game Boy Advance'],
            ['nombre' => 'Nintendo DS'],
            ['nombre' => 'Nintendo 3DS'],
            ['nombre' => 'Virtual Boy'],
            // Sega
            ['nombre' => 'Master System'],
            ['nombre' => 'Mega Drive'],
            ['nombre' => 'Saturn'],
            ['nombre' => 'Dreamcast'],
            ['nombre' => 'Game Gear'],
            // PlayStation
            ['nombre' => 'PlayStation 1'],
            ['nombre' => 'PlayStation 2'],
            ['nombre' => 'PlayStation 3'],
            ['nombre' => 'PSP'],
            // Xbox
            ['nombre' => 'Xbox'],
            ['nombre' => 'Xbox 360'],
            // PC y otros
            ['nombre' => 'PC'],
            ['nombre' => 'Neo Geo'],
            ['nombre' => 'Atari 2600'],
            ['nombre' => 'Atari 7800'],
        ]);

        // Usuarios
        DB::table('USUARIO')->insertOrIgnore([
            // Administrador
            [
                'nombre' => 'Admin',
                'email' => 'admin@retrocoll.com',
                'rol' => 'admin',
                'password' => Hash::make('Admin1234!'),
                'fecha_registro' => now(),
                'valoracion_promedio' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Usuarios normales de prueba
            [
                'nombre' => 'María García',
                'email' => 'maria@retrocoll.com',
                'rol' => 'usuario',
                'password' => Hash::make('Usuario1234!'),
                'fecha_registro' => now(),
                'valoracion_promedio' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Carlos López',
                'email' => 'carlos@retrocoll.com',
                'rol' => 'usuario',  
                'password' => Hash::make('Usuario1234!'),
                'fecha_registro' => now(),
                'valoracion_promedio' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Lucía Martínez',
                'email' => 'lucia@retrocoll.com',
                'rol' => 'usuario',
                'password' => Hash::make('Usuario1234!'),
                'fecha_registro' => now(),
                'valoracion_promedio' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}