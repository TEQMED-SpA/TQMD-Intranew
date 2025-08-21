<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'rut'
    ];

    // Relaciones
    public function centrosMedicos()
    {
        return $this->hasMany(CentroMedico::class);
    }

    // Accessors
    public function getRutFormateadoAttribute()
    {
        if (!$this->rut) return null;
        
        $rut = preg_replace('/[^0-9kK]/', '', $this->rut);
        if (strlen($rut) > 1) {
            return substr($rut, 0, -1) . '-' . substr($rut, -1);
        }
        return $rut;
    }

    public function getTotalCentrosAttribute()
    {
        return $this->centrosMedicos()->count();
    }

    // Scopes
    public function scopeConCentros($query)
    {
        return $query->has('centrosMedicos');
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%")
              ->orWhere('rut', 'like', "%{$termino}%");
        });
    }
}