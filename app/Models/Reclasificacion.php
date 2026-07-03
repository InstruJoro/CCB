<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reclasificacion extends Model
{
    protected $table = 'reclasificaciones';

    protected $fillable = [
        'incidente_id', 'tipo_anterior', 'tipo_nuevo', 'usuario_id', 'justificacion',
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
