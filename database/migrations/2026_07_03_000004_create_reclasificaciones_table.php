<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclasificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $table->string('tipo_anterior', 50);
            $table->string('tipo_nuevo', 50);
            $table->foreignId('usuario_id')->constrained('users');
            $table->text('justificacion');            // obligatoria: trazabilidad del cambio
            $table->timestamps();

            $table->index('incidente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclasificaciones');
    }
};
