<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroMedico extends Model
{
    use HasFactory;

    protected $table = 'centros_medicos';

    protected $fillable = [
        'cliente_id',
        'cod_cliente',
        'cod_centro_medico',
        'nombre',
        'direccion',
        'ciudad',
        'region',
        'telefono',
        'activo'
    ];

    protected $casts = [
        'cod_cliente' => 'integer',
        'cod_centro_medico' => 'integer',
        'activo' => 'boolean'
    ];

    protected $attributes = [
        'activo' => 1,
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    public function equipos()
    {
        return $this->hasMany(\App\Models\Equipo::class, 'centro_medico_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'clinica_id');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }

    // Scopes
    public function scopePorRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
                ->orWhere('region', 'like', "%{$termino}%")
                ->orWhere('ciudad', 'like', "%{$termino}%");
        });
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return $this->razon_social ?: $this->centro_dialisis;
    }

    public function getTotalSolicitudesAttribute()
    {
        return $this->solicitudes()->count();
    }

    public function getSolicitudesPendientesAttribute()
    {
        return $this->solicitudes()->where('estado', 'pendiente')->count();
    }
}
