<?php

namespace App\Enums;

/**
 * Taxonomía revisada de tipos de incidente — Propuesta V2, sección 6.
 */
enum TipoIncidente: string
{
    case Phishing        = 'phishing';
    case Malware         = 'malware';
    case AccesoNoAutorizado = 'acceso_no_autorizado';
    case FugaInformacion = 'fuga_informacion';
    case FraudeDigital   = 'fraude_digital';
    case Disponibilidad  = 'disponibilidad';
    case Defacement      = 'defacement';
    case PerdidaDispositivo = 'perdida_dispositivo';
    case Vulnerabilidad  = 'vulnerabilidad';
    case Otro            = 'otro';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Phishing           => 'Phishing o ingeniería social',
            self::Malware            => 'Malware o código malicioso',
            self::AccesoNoAutorizado => 'Acceso no autorizado',
            self::FugaInformacion    => 'Fuga o exposición de información',
            self::FraudeDigital      => 'Fraude digital',
            self::Disponibilidad     => 'Disponibilidad del servicio',
            self::Defacement         => 'Defacement o alteración web',
            self::PerdidaDispositivo => 'Pérdida o robo de dispositivo',
            self::Vulnerabilidad     => 'Vulnerabilidad o configuración insegura',
            self::Otro               => 'Otro (especificar)',
        };
    }

    /**
     * Regla de priorización automática — V2, sección 7:
     * tipos que por naturaleza marcan el caso como prioritario.
     */
    public function esPrioritarioPorDefecto(): bool
    {
        return in_array($this, [
            self::AccesoNoAutorizado,
            self::FugaInformacion,
            self::Disponibilidad,
            self::Defacement,
        ], true);
    }
}
