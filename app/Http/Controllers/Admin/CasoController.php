<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoIncidente;
use App\Enums\TipoIncidente;
use App\Http\Controllers\Controller;
use App\Models\Adjunto;
use App\Models\Incidente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CasoController extends Controller
{
    public function show(Incidente $incidente)
    {
        $incidente->load(['seguimientos.usuario', 'adjuntos', 'reclasificaciones.usuario', 'responsable']);

        return view('admin.detalle', [
            'incidente' => $incidente,
            'tipos'     => TipoIncidente::cases(),
            'transiciones' => $incidente->estado->transicionesPermitidas(),
        ]);
    }

    /**
     * Cambio de estado con historial completo (V2, sección 5.3):
     * fecha, usuario, estado anterior, estado resultante y observación.
     */
    public function cambiarEstado(Request $request, Incidente $incidente)
    {
        $datos = $request->validate([
            'estado'      => ['required', Rule::enum(EstadoIncidente::class)],
            'observacion' => ['required', 'string', 'max:1000'],
            'severidad'   => ['nullable', Rule::in(['baja', 'media', 'alta', 'critica'])],
        ]);

        $nuevoEstado = EstadoIncidente::from($datos['estado']);

        // Solo transiciones válidas del catálogo formal
        if (! in_array($nuevoEstado, $incidente->estado->transicionesPermitidas(), true)) {
            return back()->withErrors([
                'estado' => "Transición no permitida desde '{$incidente->estado->etiqueta()}'.",
            ]);
        }

        DB::transaction(function () use ($incidente, $nuevoEstado, $datos) {
            $anterior = $incidente->estado;

            $incidente->estado = $nuevoEstado;
            $incidente->responsable_id ??= auth()->id();
            if ($datos['severidad'] ?? null) {
                $incidente->severidad = $datos['severidad'];
            }
            if ($nuevoEstado === EstadoIncidente::Cerrado) {
                $incidente->fecha_cierre = now();
            }
            $incidente->save();

            $incidente->seguimientos()->create([
                'usuario_id'        => auth()->id(),
                'accion'            => 'cambio_estado',
                'detalle'           => $datos['observacion'],
                'estado_anterior'   => $anterior->value,
                'estado_resultante' => $nuevoEstado->value,
            ]);
        });

        return back()->with('ok', 'Estado actualizado.');
    }

    /**
     * Reclasificación interna con justificación obligatoria (V2, sección 6).
     */
    public function reclasificar(Request $request, Incidente $incidente)
    {
        $datos = $request->validate([
            'tipo_nuevo'    => ['required', Rule::enum(TipoIncidente::class)],
            'justificacion' => ['required', 'string', 'min:15', 'max:1000'],
        ]);

        DB::transaction(function () use ($incidente, $datos) {
            $incidente->reclasificaciones()->create([
                'tipo_anterior' => $incidente->tipo_incidente->value,
                'tipo_nuevo'    => $datos['tipo_nuevo'],
                'usuario_id'    => auth()->id(),
                'justificacion' => $datos['justificacion'],
            ]);

            $incidente->tipo_incidente = TipoIncidente::from($datos['tipo_nuevo']);
            $incidente->save();

            $incidente->seguimientos()->create([
                'usuario_id' => auth()->id(),
                'accion'     => 'reclasificacion',
                'detalle'    => $datos['justificacion'],
            ]);
        });

        return back()->with('ok', 'Caso reclasificado con trazabilidad.');
    }

    /**
     * Descarga de evidencias SOLO para usuarios autenticados,
     * desde disco privado, sin URL directa (V2, sección 8.2).
     */
    public function descargarAdjunto(Adjunto $adjunto)
    {
        $disco = config('incidentes.adjuntos.disco');

        abort_unless(Storage::disk($disco)->exists($adjunto->nombre_almacenado), 404);

        return Storage::disk($disco)->download(
            $adjunto->nombre_almacenado,
            $adjunto->nombre_original,
            ['Content-Type' => $adjunto->tipo_mime,
             // Forzar descarga: nunca renderizar la evidencia en el navegador
             'Content-Disposition' => 'attachment']
        );
    }
}
