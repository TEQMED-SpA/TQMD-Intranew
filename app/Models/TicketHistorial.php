<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistorial extends Model
{
    use HasFactory;

    protected $table = 'ticket_historial';

    public $timestamps = false; // La tabla usa 'fecha' en lugar de created_at/updated_at

    protected $fillable = [
        'ticket_id',
        'usuario',
        'rol',
        'accion',
        'estado_anterior',
        'estado_nuevo',
        'tecnico_anterior',
        'tecnico_nuevo',
        'comentario',
        'foto',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * Relación con el ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
