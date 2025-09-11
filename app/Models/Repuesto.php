<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    use HasFactory;

    protected $table = 'repuesto';
    protected $primaryKey = 'repuesto_id';

    protected $fillable = [
        'serie',
        'nombre',
        'modelo',
        'marca',
        'estado',
        'ubicacion',
        'descripcion',
        'stock',
        'foto',
        'categoria_id',
        'usuario_id'
    ];

    protected $casts = [
        'stock' => 'integer'
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'categoria_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'repuesto_id', 'repuesto_id');
    }

    public function solicitudRepuestos()
    {
        return $this->hasMany(SolicitudRepuesto::class, 'repuesto_id', 'repuesto_id');
    }

    // Scopes
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeStockBajo($query, $umbral = 5)
    {
        return $query->where('stock', '<=', $umbral)->where('stock', '>', 0);
    }

    public function scopeSinStock($query)
    {
        return $query->where('stock', 0);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
                ->orWhere('serie', 'like', "%{$termino}%")
                ->orWhere('modelo', 'like', "%{$termino}%")
                ->orWhere('marca', 'like', "%{$termino}%");
        });
    }

    // Accessors
    public function getIdentificacionCompletaAttribute()
    {
        return "{$this->serie} - {$this->nombre}";
    }

    public function getStockBajoAttribute()
    {
        return $this->stock <= 5 && $this->stock > 0;
    }

    public function getStockCriticoAttribute()
    {
        return $this->stock <= 2 && $this->stock > 0;
    }

    public function getSinStockAttribute()
    {
        return $this->stock == 0;
    }

    public function getTotalSalidasAttribute()
    {
        return $this->salidas()->sum('cantidad');
    }

    // Mutators
    public function setRepuestoSerieAttribute($value)
    {
        $this->attributes['serie'] = strtoupper(trim($value));
    }

    public function setRepuestoNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower(trim($value)));
    }
}
