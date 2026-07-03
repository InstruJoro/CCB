<?php

namespace App\Services;

use App\Models\Adjunto;
use App\Models\Incidente;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Almacenamiento seguro de evidencias — Propuesta V2, sección 8.2.
 *
 * Controles implementados:
 *  1. Verificación de MIME REAL (finfo sobre contenido, no extensión).
 *  2. Lista blanca de tipos y extensiones (nunca lista negra).
 *  3. Nombre aleatorio en disco: el nombre original jamás toca el filesystem
 *     (previene path traversal y ejecución por doble extensión).
 *  4. Disco privado fuera de public/, sin URL directa.
 *  5. Hash SHA-256 para integridad de la cadena de evidencia.
 */
class AlmacenAdjuntos
{
    public function guardar(Incidente $incidente, UploadedFile $archivo): Adjunto
    {
        $config = config('incidentes.adjuntos');

        // MIME real leído del contenido del archivo en servidor
        $mimeReal = $archivo->getMimeType();

        if (! in_array($mimeReal, $config['mimes_permitidos'], true)) {
            abort(422, 'Tipo de archivo no permitido.');
        }

        $extension = strtolower($archivo->getClientOriginalExtension());
        if (! in_array($extension, $config['extensiones_permitidas'], true)) {
            abort(422, 'Extensión de archivo no permitida.');
        }

        // Nombre aleatorio: nunca se usa el nombre enviado por el cliente
        $nombreAlmacenado = Str::uuid()->toString() . '.' . $extension;

        $ruta = $archivo->storeAs(
            'evidencias/' . now()->format('Y/m'),
            $nombreAlmacenado,
            $config['disco']
        );

        return Adjunto::create([
            'incidente_id'      => $incidente->id,
            // Saneado para visualización: solo el basename, sin rutas
            'nombre_original'   => Str::limit(basename($archivo->getClientOriginalName()), 200),
            'nombre_almacenado' => $ruta,
            'tipo_mime'         => $mimeReal,
            'tamanio_bytes'     => $archivo->getSize(),
            'hash_sha256'       => hash_file('sha256', $archivo->getRealPath()),
        ]);
    }
}
