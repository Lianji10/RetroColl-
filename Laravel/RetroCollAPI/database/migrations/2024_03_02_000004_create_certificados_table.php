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
        Schema::create('CERTIFICADO', function (Blueprint $table) {
            $table->id('id_certificado');
            $table->string('archivo_url', 255)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->boolean('es_valido')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CERTIFICADO');
    }
};
