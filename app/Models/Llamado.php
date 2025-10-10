<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llamado extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_llamado',
        'fecha_llamado',
        'hora_llamado',
        'centro_medico_id',
        'nombre_informante',
        'id_equipo',
        'desperfecto',
        'tecnico_asignado_id',
        'categoria_llamado_id',
    ];

    public function centroMedico()
    {
        return $this->belongsTo(CentroMedico::class, 'centro_medico_id');
    }

    public function tecnicoAsignado()
    {
        return $this->belongsTo(User::class, 'tecnico_asignado_id');
    }

    public function categoriaLlamado()
    {
        return $this->belongsTo(CategoriaLlamado::class, 'categoria_llamado_id');
    }
}
