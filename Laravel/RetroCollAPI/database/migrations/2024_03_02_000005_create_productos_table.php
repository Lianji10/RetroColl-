<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Ejecutar las migraciones
    public function up(): void
    {
        Schema::create('PRODUCTO', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->string('estado', 50)->nullable();
            $table->string('imagen')->nullable();
            $table->date('fecha_publicacion')->default(now());

            $table->unsignedBigInteger('id_vendedor');
            $table->unsignedBigInteger('id_categoria');
            $table->unsignedBigInteger('id_plataforma');


            // Foreign Keys
            $table->foreign('id_vendedor')->references('id_usuario')->on('USUARIO')->onDelete('cascade');
            $table->foreign('id_categoria')->references('id_categoria')->on('CATEGORIA');
            $table->foreign('id_plataforma')->references('id_plataforma')->on('PLATAFORMA');


            $table->timestamps();
        });
    }

    // Revertir las migraciones
    public function down(): void
    {
        Schema::dropIfExists('PRODUCTO');
    }
};
