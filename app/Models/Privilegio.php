<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre'
    ];

    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rol_privilegios', 'privilegio_id', 'rol_id');
    }

    // Scopes
    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', $nombre);
    }
}