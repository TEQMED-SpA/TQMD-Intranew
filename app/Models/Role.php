<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre'
    ];

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    public function privilegios()
    {
        return $this->belongsToMany(Privilegio::class, 'rol_privilegios', 'rol_id', 'privilegio_id');
    }

    // Métodos de verificación
    public function tienePrivilegio($nombrePrivilegio)
    {
        return $this->privilegios()->where('nombre', $nombrePrivilegio)->exists();
    }

    // Accessors
    public function getUsuariosActivosCountAttribute()
    {
        return $this->users()->where('activo', 1)->count();
    }
}