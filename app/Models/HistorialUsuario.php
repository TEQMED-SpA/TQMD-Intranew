<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialUsuario extends Model
{
    use HasFactory;

    protected $table = 'historial_usuarios';

    protected $fillable = [
        'usuario_id',
        'nombre_usuario',
        'nombre_tabla',
        'accion',
        'fecha_hora'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorTabla($query, $tabla)
    {
        return $query->where('nombre_tabla', $tabla);
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