<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioTecnico extends Model
{
    protected $table = 'inventario_tecnico';
    protected $fillable = [
        'tecnico_id',
        'repuesto_id',
        'cantidad',
        'solicitud_id',
        'estado',
        'observacion'
    ];

    public function tecnico()
    {
        return $this->belongsTo(\App\Models\User::class, 'tecnico_id');
    }
    public function repuesto()
    {
        return $this->belongsTo(\App\Models\Repuesto::class, 'repuesto_id');
    }
    public function solicitud()
    {
        return $this->belongsTo(\App\Models\Solicitud::class, 'solicitud_id');
    }
}
