<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historial';

    protected $fillable = [
        'usuario_id',
        'nombre_tabla',
        'accion',
        'fecha_hora',
        'antes',
        'despues',
        'nombre_usuario'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'antes' => 'array',
        'despues' => 'array'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePorTabla($query, $tabla)
    {
        return $query->where('nombre_tabla', $tabla);
    }

    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
    }

    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_hora', '>=', now()->subDays($dias));
    }

    // Accessors
    public function getTiempoTranscurridoAttribute()
    {
        return $this->fecha_hora->diffForHumans();
    }
}
