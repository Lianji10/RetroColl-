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
        Schema::create('VALORACION', function (Blueprint $table) {
            $table->id('id_valoracion');
            $table->integer('puntuacion');
            $table->text('comentario')->nullable();
            $table->date('fecha')->default(now());

            $table->unsignedBigInteger('id_emisor');
            $table->unsignedBigInteger('id_receptor');

            $table->foreign('id_emisor')->references('id_usuario')->on('USUARIO');
            $table->foreign('id_receptor')->references('id_usuario')->on('USUARIO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('VALORACION');
    }
};
