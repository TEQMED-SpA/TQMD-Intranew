<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_ticket',
        'cliente',
        'nombre_apellido',
        'telefono',
        'cargo',
        'email',
        'id_numero_equipo',
        'modelo_maquina',
        'falla_presentada',
        'momento_falla',
        'momento_falla_otras',
        'acciones_realizadas',
        'estado',
        'llamado_id',
        'tecnico_asignado_id',
        'fecha_visita',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'fecha_visita' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relación con el técnico asignado
    public function tecnicoAsignado()
    {
        return $this->belongsTo(User::class, 'tecnico_asignado_id');
    }

    // Relación con llamado
    public function llamado()
    {
        return $this->belongsTo(Llamado::class, 'llamado_id');
    }

    // Scope para buscar
    public function scopeBuscar($query, $buscar)
    {
        return $query->where('numero_ticket', 'like', "%{$buscar}%")
            ->orWhere('cliente', 'like', "%{$buscar}%")
            ->orWhere('nombre_apellido', 'like', "%{$buscar}%")
            ->orWhere('telefono', 'like', "%{$buscar}%");
    }

}
