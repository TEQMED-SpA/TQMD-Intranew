<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformePreventivoInspeccion extends Model
{
    use HasFactory;

    protected $table = 'informe_preventivo_inspecciones';

    public $timestamps = false; // ← IMPORTANTE para evitar el error SQL

    protected $fillable = [
        'informe_preventivo_id',
        'descripcion',
        'respuesta',
    ];

    //Relaciones
    public function informePreventivo(): BelongsTo
    {
        return $this->belongsTo(InformePreventivo::class, 'informe_preventivo_id');
    }
}
