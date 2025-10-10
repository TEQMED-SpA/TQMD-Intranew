<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaRepuesto extends Model
{
    use HasFactory;

    protected $table = 'categorias_repuestos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'categoria_subcategoria'
    ];

    // Relaciones
    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'categoria_id', 'id');
    }

    // Scopes
    public function scopeConrepuestos($query)
    {
        return $query->has('repuestos');
    }

    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', "%{$nombre}%");
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return $this->subcategoria
            ? $this->nombre . ' - ' . $this->subcategoria
            : $this->nombre;
    }

    public function getTotalrepuestosAttribute()
    {
        return $this->repuestos()->count();
    }

    public function getrepuestosConStockAttribute()
    {
        return $this->repuestos()->where('stock', '>', 0)->count();
    }
}
