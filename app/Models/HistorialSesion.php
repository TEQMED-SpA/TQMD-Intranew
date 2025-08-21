<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialSesion extends Model
{
    use HasFactory;

    protected $table = 'historial_sesiones';

    protected $fillable = [
        'id_usuario',
        'fecha_hora',
        'navegador',
        'ip'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Scopes
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('id_usuario', $usuarioId);
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

    public function getNavegadorSimplificadoAttribute()
    {
        // Simplificar el string del navegador
        if (str_contains($this->navegador, 'Chrome')) return 'Chrome';
        if (str_contains($this->navegador, 'Firefox')) return 'Firefox';
        if (str_contains($this->navegador, 'Safari')) return 'Safari';
        if (str_contains($this->navegador, 'Edge')) return 'Edge';
        return 'Otro';
    }
}