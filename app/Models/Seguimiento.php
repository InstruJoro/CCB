<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seguimiento extends Model
{
    protected $fillable = [
        'incidente_id', 'usuario_id', 'accion', 'detalle',
        'estado_anterior', 'estado_resultante',
    ];

    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
