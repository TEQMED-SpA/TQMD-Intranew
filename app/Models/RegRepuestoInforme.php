<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegRepuestoInforme extends Model
{
    protected $table = 'reg_repuestos_informes';

    protected $fillable = [
        'informe_correctivo_id',
        'informe_preventivo_id',
        'repuesto_id',
        'cantidad',
    ];

    public function informeCorrectivo(): BelongsTo
    {
        return $this->belongsTo(InformeCorrectivo::class, 'informe_correctivo_id');
    }

    public function informePreventivo(): BelongsTo
    {
        return $this->belongsTo(InformePreventivo::class, 'informe_preventivo_id');
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class, 'repuesto_id');
    }
}
