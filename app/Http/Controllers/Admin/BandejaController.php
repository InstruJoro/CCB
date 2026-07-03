<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoIncidente;
use App\Http\Controllers\Controller;
use App\Models\Incidente;
use Illuminate\Http\Request;

/**
 * Bandeja interna de casos — capa restringida (middleware auth).
 */
class BandejaController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'estado' => ['nullable', 'string'],
            'q'      => ['nullable', 'string', 'max:100'],
        ]);

        $casos = Incidente::query()
            ->when($filtros['estado'] ?? null, fn ($q, $e) => $q->where('estado', $e))
            ->when($filtros['q'] ?? null, function ($q, $texto) {
                // Parámetros ligados: sin concatenación de SQL
                $q->where(function ($sub) use ($texto) {
                    $sub->where('codigo', 'ilike', "%{$texto}%")
                        ->orWhere('titulo', 'ilike', "%{$texto}%");
                });
            })
            ->orderByDesc('prioritario')
            ->orderByDesc('fecha_reporte')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bandeja', [
            'casos'   => $casos,
            'estados' => EstadoIncidente::cases(),
        ]);
    }
}
