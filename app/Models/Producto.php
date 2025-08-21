<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';
    protected $primaryKey = 'producto_id';

    protected $fillable = [
        'producto_serie',
        'producto_nombre',
        'producto_modelo',
        'producto_marca',
        'producto_estado',
        'producto_ubicacion',
        'producto_descripcion',
        'producto_stock',
        'producto_foto',
        'categoria_id',
        'usuario_id'
    ];

    protected $casts = [
        'producto_stock' => 'integer'
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
        return $this->hasMany(Salida::class, 'producto_id', 'producto_id');
    }

    public function solicitudRepuestos()
    {
        return $this->hasMany(SolicitudRepuesto::class, 'producto_id', 'producto_id');
    }

    // Scopes
    public function scopeConStock($query)
    {
        return $query->where('producto_stock', '>', 0);
    }

    public function scopeStockBajo($query, $umbral = 5)
    {
        return $query->where('producto_stock', '<=', $umbral)->where('producto_stock', '>', 0);
    }

    public function scopeSinStock($query)
    {
        return $query->where('producto_stock', 0);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('producto_estado', $estado);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('producto_nombre', 'like', "%{$termino}%")
              ->orWhere('producto_serie', 'like', "%{$termino}%")
              ->orWhere('producto_modelo', 'like', "%{$termino}%")
              ->orWhere('producto_marca', 'like', "%{$termino}%");
        });
    }

    // Accessors
    public function getIdentificacionCompletaAttribute()
    {
        return "{$this->producto_serie} - {$this->producto_nombre}";
    }

    public function getStockBajoAttribute()
    {
        return $this->producto_stock <= 5 && $this->producto_stock > 0;
    }

    public function getStockCriticoAttribute()
    {
        return $this->producto_stock <= 2 && $this->producto_stock > 0;
    }

    public function getSinStockAttribute()
    {
        return $this->producto_stock == 0;
    }

    public function getTotalSalidasAttribute()
    {
        return $this->salidas()->sum('cantidad');
    }

    // Mutators
    public function setProductoSerieAttribute($value)
    {
        $this->attributes['producto_serie'] = strtoupper(trim($value));
    }

    public function setProductoNombreAttribute($value)
    {
        $this->attributes['producto_nombre'] = ucwords(strtolower(trim($value)));
    }
}