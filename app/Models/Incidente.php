<?php

namespace App\Models;

use App\Enums\EstadoIncidente;
use App\Enums\TipoIncidente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incidente extends Model
{
    /**
     * Asignación masiva controlada: los campos de gestión interna
     * (severidad, estado, responsable_id, prioritario) NO son fillable.
     * Se asignan explícitamente desde los controladores autorizados.
     */
    protected $fillable = [
        'titulo', 'nombre_reportante', 'correo', 'telefono', 'tipo_usuario',
        'organizacion_reportante', 'tipo_incidente', 'tipo_incidente_detalle',
        'fecha_ocurrencia', 'descripcion', 'sigue_activo',
        'organizacion_afectada', 'ciudad_incidente', 'activo_afectado',
        'urgencia_reportante',
    ];

    protected $casts = [
        'sigue_activo'     => 'boolean',
        'prioritario'      => 'boolean',
        'fecha_ocurrencia' => 'datetime',
        'fecha_reporte'    => 'datetime',
        'fecha_cierre'     => 'datetime',
        'estado'           => EstadoIncidente::class,
        'tipo_incidente'   => TipoIncidente::class,
    ];

    /**
     * Nunca exponer en serializaciones datos de contacto ni auditoría.
     */
    protected $hidden = ['correo', 'telefono', 'ip_origen'];

    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class)->orderByDesc('created_at');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(Adjunto::class);
    }

    public function reclasificaciones(): HasMany
    {
        return $this->hasMany(Reclasificacion::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
