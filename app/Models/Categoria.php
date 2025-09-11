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
    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'categoria_id', 'categoria_id');
    }

    // Scopes
    public function scopeConrepuestos($query)
    {
        return $query->has('repuestos');
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

    public function getTotalrepuestosAttribute()
    {
        return $this->repuestos()->count();
    }

    public function getrepuestosConStockAttribute()
    {
        return $this->repuestos()->where('stock', '>', 0)->count();
    }
}
