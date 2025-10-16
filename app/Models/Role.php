<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['nombre'];

    public function privilegios()
    {
        return $this->belongsToMany(Privilegio::class, 'rol_privilegios', 'rol_id', 'privilegio_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
