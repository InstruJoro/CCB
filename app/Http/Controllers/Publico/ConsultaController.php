<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Incidente;
use Illuminate\Http\Request;

/**
 * Consulta de estado sin cuenta de usuario — Propuesta V2, sección 10.
 *
 * Seguridad: se exige código + correo del reportante. El código solo
 * no basta (su formato es predecible por diseño); exigir el correo
 * evita que un tercero enumere códigos y consulte casos ajenos.
 * Además la ruta tiene rate limiting (ver routes/web.php).
 */
class ConsultaController extends Controller
{
    public function form()
    {
        return view('publico.consulta');
    }

    public function buscar(Request $request)
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'regex:/^CCB-\d{8}-\d{4}$/'],
            'correo' => ['required', 'email', 'max:150'],
        ]);

        $incidente = Incidente::where('codigo', $datos['codigo'])
            ->where('correo', $datos['correo'])
            ->first();

        // Mensaje idéntico exista o no el código: no se revela
        // si un código es válido para un correo distinto.
        if (! $incidente) {
            return back()
                ->withInput()
                ->withErrors(['codigo' => 'No se encontró un caso con esos datos.']);
        }

        return view('publico.consulta', [
            'resultado' => [
                'codigo'  => $incidente->codigo,
                'titulo'  => $incidente->titulo,
                'estado'  => $incidente->estado->etiqueta(),
                'fecha'   => $incidente->fecha_reporte->format('d/m/Y H:i'),
            ],
        ]);
    }
}
