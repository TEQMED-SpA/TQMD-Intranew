<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'cliente_id',
        'centro_medico_id',
        'nombre',
        'modelo',
        'marca',
        'id_maquina',
        'numero_serie',
        'horas_uso',
        'estado',
        'sku',
        'descripcion',
        'ultima_mantencion',
        'proxima_mantencion',
        'tipo_mantencion',
        'imagen',
    ];

    public function centro()
    {
        return $this->belongsTo(\App\Models\CentroMedico::class, 'centro_medico_id');
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }
}
