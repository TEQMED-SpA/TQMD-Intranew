<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipoTipoPreventivo extends Model
{
    protected $table = 'equipos_tipos_preventivos';

    protected $fillable = [
        'equipo_id',
        'tipo_informe_preventivo_id',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function tipoInformePreventivo(): BelongsTo
    {
        return $this->belongsTo(TipoInformePreventivo::class, 'tipo_informe_preventivo_id');
    }
}
