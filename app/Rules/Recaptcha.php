<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

/**
 * Verificación de CAPTCHA en servidor — Propuesta V2, secciones 5.1 y 8.1.
 * La validación del token SIEMPRE ocurre en servidor contra la API de
 * Google; el widget del cliente es solo el recolector del token.
 */
class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $respuesta = Http::asForm()
            ->timeout(5)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('incidentes.recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

        $datos = $respuesta->json();

        $valido = ($datos['success'] ?? false) === true
            && ($datos['score'] ?? 1) >= config('incidentes.recaptcha.umbral_v3');

        if (! $valido) {
            $fail('No fue posible verificar que usted es una persona. Intente nuevamente.');
        }
    }
}
