<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categoria';
    protected $primaryKey = 'categoria_id';

    protected $fillable = [
        'categoria_nombre',
        'categoria_subcategoria'
    ];

    // Relaciones
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id', 'categoria_id');
    }

    // Scopes
    public function scopeConProductos($query)
    {
        return $query->has('productos');
    }

    public function scopePorNombre($query, $nombre)
    {
        return $query->where('categoria_nombre', 'like', "%{$nombre}%");
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return $this->categoria_subcategoria 
            ? $this->categoria_nombre . ' - ' . $this->categoria_subcategoria
            : $this->categoria_nombre;
    }

    public function getTotalProductosAttribute()
    {
        return $this->productos()->count();
    }

    public function getProductosConStockAttribute()
    {
        return $this->productos()->where('producto_stock', '>', 0)->count();
    }
}