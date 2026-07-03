<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidentes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();          // CCB-YYYYMMDD-NNNN
            $table->string('titulo', 150);
            $table->timestamp('fecha_reporte')->useCurrent();

            // Bloque 1 — Identificación del reportante
            $table->string('nombre_reportante', 120);
            $table->string('correo', 150);
            $table->string('telefono', 30)->nullable();
            $table->string('tipo_usuario', 30);              // ciudadano|empresa|institucion|miembro|otro
            $table->string('organizacion_reportante', 150)->nullable();

            // Bloque 2 — Datos del incidente
            $table->string('tipo_incidente', 50);
            $table->string('tipo_incidente_detalle', 200)->nullable(); // obligatorio si tipo = otro
            $table->timestamp('fecha_ocurrencia');
            $table->text('descripcion');
            $table->boolean('sigue_activo')->default(false);

            // Bloque 3 — Entidad y activo afectado
            $table->string('organizacion_afectada', 150)->nullable();
            $table->string('ciudad_incidente', 100)->nullable();
            $table->string('activo_afectado', 200);

            // Bloque 4 — Soporte del caso
            $table->string('urgencia_reportante', 10)->nullable(); // baja|media|alta

            // Gestión interna
            $table->string('severidad', 10)->nullable();     // baja|media|alta|critica
            $table->string('estado', 20)->default('recibido');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('prioritario')->default(false);
            $table->text('observaciones_internas')->nullable();
            $table->timestamp('fecha_cierre')->nullable();

            // Metadatos de auditoría (no expuestos al público)
            $table->string('ip_origen', 45)->nullable();
            $table->timestamps();

            $table->index(['estado', 'severidad']);
            $table->index('tipo_incidente');
            $table->index('fecha_reporte');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidentes');
    }
};
