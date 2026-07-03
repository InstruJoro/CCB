<?php

namespace App\Http\Requests;

use App\Enums\TipoIncidente;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación en servidor de TODOS los campos del formulario público,
 * independiente de cualquier validación en cliente (V2, sección 8.3).
 *
 * Notas de seguridad:
 *  - Eloquent usa consultas preparadas: no hay concatenación de SQL.
 *  - Blade escapa la salida con {{ }}: la "sanitización" correcta es
 *    validar a la entrada y escapar a la salida, no mutilar el texto.
 *  - Los archivos se validan aquí por tamaño/cantidad/MIME declarado,
 *    y de nuevo en AlmacenAdjuntos por MIME real del contenido.
 */
class StoreIncidenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // formulario público
    }

    public function rules(): array
    {
        $adj = config('incidentes.adjuntos');

        return [
            // Bloque 1 — Identificación del reportante
            'nombre_reportante' => ['required', 'string', 'max:120'],
            'correo'            => ['required', 'email:rfc,dns', 'max:150'],
            'telefono'          => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]*$/'],
            'tipo_usuario'      => ['required', Rule::in(['ciudadano', 'empresa', 'institucion', 'miembro', 'otro'])],
            'organizacion_reportante' => ['nullable', 'string', 'max:150'],

            // Bloque 2 — Datos del incidente
            'titulo'            => ['required', 'string', 'max:150'],
            'tipo_incidente'    => ['required', Rule::enum(TipoIncidente::class)],
            // Obligatorio solo cuando tipo = otro (V2, sección 6)
            'tipo_incidente_detalle' => [
                'required_if:tipo_incidente,otro', 'nullable', 'string', 'max:200',
            ],
            'fecha_ocurrencia'  => ['required', 'date', 'before_or_equal:now'],
            'descripcion'       => ['required', 'string', 'min:30', 'max:5000'],
            'sigue_activo'      => ['required', 'boolean'],

            // Bloque 3 — Entidad y activo afectado
            'organizacion_afectada' => ['nullable', 'string', 'max:150'],
            'ciudad_incidente'  => ['nullable', 'string', 'max:100'],
            'activo_afectado'   => ['required', 'string', 'max:200'],

            // Bloque 4 — Soporte del caso
            'urgencia_reportante' => ['nullable', Rule::in(['baja', 'media', 'alta'])],
            'evidencias'        => ['nullable', 'array', 'max:' . $adj['maximo_archivos']],
            'evidencias.*'      => [
                'file',
                'max:' . $adj['tamanio_maximo_kb'],
                'mimes:' . implode(',', $adj['extensiones_permitidas']),
            ],

            // CAPTCHA obligatorio (V2, sección 8.1)
            'g-recaptcha-response' => ['required', new Recaptcha()],
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.min' => 'La descripción debe tener al menos 30 caracteres para permitir el triage inicial.',
            'tipo_incidente_detalle.required_if' => 'Al seleccionar "Otro" debe describir el tipo de incidente.',
            'evidencias.max' => 'Puede adjuntar hasta 3 archivos.',
            'evidencias.*.max' => 'Cada archivo puede pesar máximo 10 MB.',
        ];
    }
}
