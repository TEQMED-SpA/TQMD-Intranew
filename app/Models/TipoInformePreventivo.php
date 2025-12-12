<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoInformePreventivo extends Model
{
    protected $table = 'tipo_informe_preventivo';

    protected $fillable = [
        'nombre',
        'activo',
        'tipo_equipo_id',
    ];

    public function informesPreventivos(): HasMany
    {
        return $this->hasMany(InformePreventivo::class, 'tipo_informe_preventivo_id');
    }

    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'tipo_equipo_id');
    }
}
