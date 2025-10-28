<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoSolicitud extends Model
{
    protected $table = 'estados_solicitudes';

    protected $fillable = ['nombre'];

    public $timestamps = false;

    // Relación con las solicitudes
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'estado_id');
    }
}
