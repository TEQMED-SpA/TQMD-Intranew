<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoRepuesto extends Model
{
    protected $table = 'estados_repuestos'; // ajusta si tu tabla se llama distinto
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'estado_id');
    }
}
