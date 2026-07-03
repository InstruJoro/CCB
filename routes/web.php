<?php

use App\Http\Controllers\Admin\BandejaController;
use App\Http\Controllers\Admin\CasoController;
use App\Http\Controllers\Publico\ConsultaController;
use App\Http\Controllers\Publico\ReporteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Capa pública
|--------------------------------------------------------------------------
| CSRF: activo por defecto en el grupo 'web' de Laravel (token @csrf).
| Rate limiting por IP según Propuesta V2, sección 8.3.
*/

Route::get('/reportar', [ReporteController::class, 'create'])
    ->name('reporte.create');

Route::post('/reportar', [ReporteController::class, 'store'])
    ->middleware('throttle:' . config('incidentes.rate_limit.reportes_por_hora', 5) . ',60')
    ->name('reporte.store');

Route::get('/consulta', [ConsultaController::class, 'form'])
    ->name('consulta.form');

Route::post('/consulta', [ConsultaController::class, 'buscar'])
    ->middleware('throttle:' . config('incidentes.rate_limit.consultas_por_minuto', 10) . ',1')
    ->name('consulta.buscar');

/*
|--------------------------------------------------------------------------
| Capa interna — requiere autenticación
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/bandeja', [BandejaController::class, 'index'])->name('bandeja');
    Route::get('/casos/{incidente}', [CasoController::class, 'show'])->name('caso.show');
    Route::post('/casos/{incidente}/estado', [CasoController::class, 'cambiarEstado'])->name('caso.estado');
    Route::post('/casos/{incidente}/reclasificar', [CasoController::class, 'reclasificar'])->name('caso.reclasificar');
    Route::get('/adjuntos/{adjunto}', [CasoController::class, 'descargarAdjunto'])->name('adjunto.descargar');
});
