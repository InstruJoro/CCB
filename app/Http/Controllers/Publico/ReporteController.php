<?php

namespace App\Http\Controllers\Publico;

use App\Enums\EstadoIncidente;
use App\Enums\TipoIncidente;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidenteRequest;
use App\Models\Incidente;
use App\Services\AlmacenAdjuntos;
use App\Services\CodigoSeguimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReporteController extends Controller
{
    public function create()
    {
        return view('publico.formulario', [
            'tipos' => TipoIncidente::cases(),
        ]);
    }

    /**
     * Flujo de recepción — Propuesta V2, secciones 9 (etapa 1) y 10.
     * Todo dentro de una transacción: o se registra el caso completo
     * (incidente + adjuntos + primer seguimiento) o no se registra nada.
     */
    public function store(StoreIncidenteRequest $request, AlmacenAdjuntos $almacen)
    {
        $incidente = DB::transaction(function () use ($request, $almacen) {

            $incidente = new Incidente($request->validated());
            $incidente->codigo = CodigoSeguimiento::generar();
            $incidente->estado = EstadoIncidente::Recibido;
            $incidente->ip_origen = $request->ip();

            // Regla de priorización automática (V2, sección 7)
            $incidente->prioritario =
                $incidente->sigue_activo
                || $incidente->tipo_incidente->esPrioritarioPorDefecto();

            $incidente->save();

            foreach ($request->file('evidencias', []) as $archivo) {
                $almacen->guardar($incidente, $archivo);
            }

            $incidente->seguimientos()->create([
                'accion'            => 'registro',
                'detalle'           => 'Reporte recibido a través del formulario público.',
                'estado_resultante' => EstadoIncidente::Recibido->value,
            ]);

            return $incidente;
        });

        // Confirmación por correo (V2, sección 10). El fallo del correo
        // no debe perder el reporte: se registra y continúa.
        try {
            Mail::to($incidente->correo)->send(new \App\Mail\ConfirmacionReporte($incidente));
        } catch (\Throwable $e) {
            Log::warning('Fallo envío de confirmación', ['codigo' => $incidente->codigo]);
        }

        return view('publico.confirmacion', ['codigo' => $incidente->codigo]);
    }
}
