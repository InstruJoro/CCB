<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $table->string('nombre_original', 255);   // nombre mostrado (saneado)
            $table->string('nombre_almacenado', 100); // nombre aleatorio real en disco
            $table->string('tipo_mime', 100);         // MIME real verificado en servidor
            $table->unsignedBigInteger('tamanio_bytes');
            $table->string('hash_sha256', 64);        // integridad de la evidencia
            $table->timestamp('fecha_carga')->useCurrent();
            $table->timestamps();

            $table->index('incidente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
    }
};
