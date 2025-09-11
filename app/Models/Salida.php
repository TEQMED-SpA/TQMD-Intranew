<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $primaryKey = 'salida_id';

    protected $fillable = [
        'solicitud_id',
        'repuesto_id',
        'usuario_pedido_id',
        'usuario_requiere_id',
        'cantidad',
        'centro_medico_id',
        'fecha_hora'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha_hora' => 'datetime'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'repuesto_id', 'id');
    }

    public function usuarioPedido()
    {
        return $this->belongsTo(User::class, 'usuario_pedido_id');
    }

    public function usuarioRequiere()
    {
        return $this->belongsTo(User::class, 'usuario_requiere_id');
    }

    public function centroMedico()
    {
        return $this->belongsTo(CentroMedico::class);
    }

    // Scopes
    public function scopePorRepuesto($query, $repuestoId)
    {
        return $query->where('repuesto_id', $repuestoId);
    }

    public function scopePorCentro($query, $centroId)
    {
        return $query->where('centro_medico_id', $centroId);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora', today());
    }

    public function scopeEsteMes($query)
    {
        return $query->whereMonth('fecha_hora', now()->month)
            ->whereYear('fecha_hora', now()->year);
    }

    // Boot method para eventos
    protected static function boot()
    {
        parent::boot();

        static::created(function ($salida) {
            // Actualizar stock del repuesto
            $repuesto = $salida->repuesto;
            if ($repuesto) {
                $repuesto->decrement('stock', $salida->cantidad);
            }
        });

        static::deleted(function ($salida) {
            // Revertir stock del repuesto
            $repuesto = $salida->repuesto;
            if ($repuesto) {
                $repuesto->increment('stock', $salida->cantidad);
            }
        });
    }
}
