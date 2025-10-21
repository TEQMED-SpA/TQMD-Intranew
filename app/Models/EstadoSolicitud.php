<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes';

    protected $fillable = [
        'numero_solicitud',
        'fecha_solicitud',
        'tecnico_id',
        'clinica_id',
        'razon',
        'estado_id',
        'motivo_rechazo',
        'aprobado_por',
        'aprobado_en'
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'aprobado_en' => 'datetime',
    ];

    // Relaciones
    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(CentroMedico::class, 'clinica_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_id');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function repuestos(): BelongsToMany
    {
        return $this->belongsToMany(Repuesto::class, 'solicitud_repuesto')
            ->withPivot(['cantidad', 'observacion', 'orden'])
            ->withTimestamps();
    }

    // Métodos para manejo de estados
    public function aprobar(User $aprobador): bool
    {
        if ($this->estado_id !== 1) { // Asumiendo que 1 es 'pendiente'
            return false;
        }

        $this->update([
            'estado_id' => 2, // Asumiendo que 2 es 'aprobada'
            'aprobado_por' => $aprobador->id,
            'aprobado_en' => now(),
        ]);

        return true;
    }

    public function rechazar(string $motivo): bool
    {
        if ($this->estado_id !== 1) { // Asumiendo que 1 es 'pendiente'
            return false;
        }

        $this->update([
            'estado_id' => 3, // Asumiendo que 3 es 'rechazada'
            'motivo_rechazo' => $motivo,
        ]);

        return true;
    }

    // Generador de número de solicitud
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($solicitud) {
            if (!$solicitud->numero_solicitud) {
                $solicitud->numero_solicitud = 'SLR-' . date('Ym') . '-' .
                    str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
