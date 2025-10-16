<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    protected $table = 'privilegios';
    protected $fillable = ['nombre'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rol_privilegios', 'privilegio_id', 'rol_id');
    }
}
