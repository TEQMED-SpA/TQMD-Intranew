<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\CentroMedico;
use App\Models\EstadoSolicitud;
use App\Models\Repuesto;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        'fecha_solicitud' => 'datetime:Y-m-d',
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

    public function repuestos()
    {
        return $this->belongsToMany(\App\Models\Repuesto::class, 'solicitud_repuesto')
            ->withPivot(['cantidad', 'observacion', 'usado', 'destino_devolucion', 'fecha_uso'])
            ->withTimestamps();
    }

    // Métodos para manejo de estados
    public function aprobar(User $aprobador): bool
    {
        if ($this->estado_id !== 1) {
            return false;
        }

        DB::transaction(function () use ($aprobador) {
            $this->update([
                'estado_id' => 2,
                'aprobado_por' => $aprobador->id,
                'aprobado_en' => now(),
            ]);

            Audit::create([
                'user_id' => $aprobador->id,
                'entity_type' => get_class($this),
                'entity_id' => $this->id,
                'action' => 'approved',
                'before_changes' => json_encode(['estado_id' => 1]),
                'after_changes' => json_encode([
                    'estado_id' => 2,
                    'aprobado_por' => $aprobador->id,
                    'aprobado_en' => now()
                ]),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        });

        return true;
    }


    public function rechazar(string $motivo): bool
    {
        if ($this->estado_id !== 1) {
            return false;
        }

        DB::transaction(function () use ($motivo) {
            $this->update([
                'estado_id' => 3,
                'motivo_rechazo' => $motivo,
            ]);
            Audit::create([
                'user_id' => Auth::id(),
                'entity_type' => get_class($this),
                'entity_id' => $this->id,
                'action' => 'rejected',
                'before_changes' => json_encode(['estado_id' => 1]),
                'after_changes' => json_encode([
                    'estado_id' => 3,
                    'motivo_rechazo' => $motivo
                ]),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        });
        return true;
    }

    // Generador de número de solicitud
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($solicitud) {
            if (!$solicitud->numero_solicitud) {
                $solicitud->numero_solicitud = 'SOL-' . date('Ym') . '-' .
                    str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
