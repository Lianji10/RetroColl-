<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Ejecutar las migraciones
    public function up(): void
    {
        Schema::create('COMPRA', function (Blueprint $table) {
            $table->id('id_compra');
            $table->timestamp('fecha_compra')->useCurrent();
            $table->decimal('precio_final', 10, 2);

            $table->unsignedBigInteger('id_comprador');
            $table->unsignedBigInteger('id_producto')->unique();

            // Foreign Keys
            $table->foreign('id_comprador')->references('id_usuario')->on('USUARIO');
            $table->foreign('id_producto')->references('id_producto')->on('PRODUCTO');

            $table->timestamps();
        });
    }

    // Revertir las migraciones
    public function down(): void
    {
        Schema::dropIfExists('COMPRA');
    }
};
