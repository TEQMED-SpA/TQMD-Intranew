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
        'producto_id',
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

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
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
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
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
            // Actualizar stock del producto
            $producto = $salida->producto;
            if ($producto) {
                $producto->decrement('producto_stock', $salida->cantidad);
            }
        });

        static::deleted(function ($salida) {
            // Revertir stock del producto
            $producto = $salida->producto;
            if ($producto) {
                $producto->increment('producto_stock', $salida->cantidad);
            }
        });
    }
}