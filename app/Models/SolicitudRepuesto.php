<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudRepuesto extends Model
{
    use HasFactory;

    protected $table = 'solicitud_repuesto';

    protected $fillable = [
        'solicitud_id',
        'producto_id',
        'cantidad',
        'orden'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'orden' => 'integer'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }

    // Scopes
    public function scopePorSolicitud($query, $solicitudId)
    {
        return $query->where('solicitud_id', $solicitudId);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden');
    }

    // Accessors
    public function getTotalValueAttribute()
    {
        return $this->cantidad * ($this->producto->precio ?? 0);
    }
}