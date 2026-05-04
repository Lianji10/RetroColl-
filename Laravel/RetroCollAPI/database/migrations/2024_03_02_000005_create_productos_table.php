<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('PRODUCTO', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->string('estado', 50)->nullable();
            $table->date('fecha_publicacion')->default(now());

            $table->unsignedBigInteger('id_vendedor');
            $table->unsignedBigInteger('id_categoria');
            $table->unsignedBigInteger('id_plataforma');
            $table->unsignedBigInteger('id_certificado')->nullable()->unique();

            $table->foreign('id_vendedor')->references('id_usuario')->on('USUARIO')->onDelete('cascade');
            $table->foreign('id_categoria')->references('id_categoria')->on('CATEGORIA');
            $table->foreign('id_plataforma')->references('id_plataforma')->on('PLATAFORMA');
            $table->foreign('id_certificado')->references('id_certificado')->on('CERTIFICADO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PRODUCTO');
    }
};
