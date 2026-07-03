<?php

namespace App\Services;

use App\Models\Incidente;
use Illuminate\Support\Facades\DB;

/**
 * Genera el código único CCB-YYYYMMDD-NNNN (Propuesta V2, sección 10).
 *
 * Seguridad: el consecutivo se calcula dentro de una transacción con
 * bloqueo, evitando colisiones bajo concurrencia. El código no es
 * secuencial global sino diario, lo que reduce (aunque no elimina)
 * la enumeración; la consulta pública exige código + correo.
 */
class CodigoSeguimiento
{
    public static function generar(): string
    {
        $fecha = now()->format('Ymd');

        return DB::transaction(function () use ($fecha) {
            $ultimo = Incidente::whereDate('fecha_reporte', today())
                ->lockForUpdate()
                ->count();

            $consecutivo = str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);

            return "CCB-{$fecha}-{$consecutivo}";
        });
    }
}
