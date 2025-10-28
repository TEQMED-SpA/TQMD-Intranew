<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'codigo',
        'nombre',
        'modelo',
        'marca',
        'id_maquina',
        'numero_serie',
        'horas_uso',
        'estado',
        'cant_dias_fuera_serv',
        'descripcion',
        'ultima_mantencion',
        'proxima_mantencion',
        'tipo_mantencion',
        'centro_medico_id',
    ];

    public function centro()
    {
        return $this->belongsTo(CentroMedico::class, 'centro_medico_id');
    }

    public function cliente()
    {
        return $this->centro()->withDefault()?->cliente();
    }
}
