<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 60);
            $table->text('detalle')->nullable();
            $table->string('estado_anterior', 20)->nullable();
            $table->string('estado_resultante', 20)->nullable();
            $table->timestamps();

            $table->index('incidente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
