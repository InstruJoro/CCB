<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjunto extends Model
{
    protected $fillable = [
        'incidente_id', 'nombre_original', 'nombre_almacenado',
        'tipo_mime', 'tamanio_bytes', 'hash_sha256',
    ];

    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class);
    }
}
