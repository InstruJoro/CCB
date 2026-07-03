<?php

namespace App\Enums;

/**
 * Catálogo de estados del incidente — Propuesta V2, sección 5.2.
 * Todos los estados son visibles al reportante vía código de seguimiento.
 */
enum EstadoIncidente: string
{
    case Recibido     = 'recibido';
    case EnValidacion = 'en_validacion';
    case Clasificado  = 'clasificado';
    case EnAtencion   = 'en_atencion';
    case Derivado     = 'derivado';
    case Escalado     = 'escalado';
    case Descartado   = 'descartado';
    case Cerrado      = 'cerrado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Recibido     => 'Recibido',
            self::EnValidacion => 'En validación',
            self::Clasificado  => 'Clasificado',
            self::EnAtencion   => 'En atención',
            self::Derivado     => 'Derivado',
            self::Escalado     => 'Escalado',
            self::Descartado   => 'Descartado',
            self::Cerrado      => 'Cerrado',
        };
    }

    /**
     * Transiciones permitidas — evita saltos de estado inconsistentes.
     */
    public function transicionesPermitidas(): array
    {
        return match ($this) {
            self::Recibido     => [self::EnValidacion, self::Descartado],
            self::EnValidacion => [self::Clasificado, self::Descartado],
            self::Clasificado  => [self::EnAtencion, self::Derivado, self::Escalado],
            self::EnAtencion   => [self::Derivado, self::Escalado, self::Cerrado],
            self::Derivado     => [self::Cerrado],
            self::Escalado     => [self::EnAtencion, self::Derivado, self::Cerrado],
            self::Descartado   => [],
            self::Cerrado      => [],
        };
    }
}
