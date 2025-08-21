<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'numero_solicitud',
        'fecha_solicitud',
        'tecnico_id',
        'clinica_id',
        'razon',
        'estado'
    ];

    protected $casts = [
        'fecha_solicitud' => 'date'
    ];

    // Constantes
    const ESTADOS = [
        'pendiente' => ['label' => 'Pendiente', 'color' => 'warning'],
        'aprobada' => ['label' => 'Aprobada', 'color' => 'success'],
        'rechazada' => ['label' => 'Rechazada', 'color' => 'danger'],
        'entregada' => ['label' => 'Entregada', 'color' => 'info'],
        'completada' => ['label' => 'Completada', 'color' => 'primary']
    ];

    // Relaciones
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function clinica()
    {
        return $this->belongsTo(CentroMedico::class, 'clinica_id');
    }

    public function repuestos()
    {
        return $this->hasMany(SolicitudRepuesto::class);
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopePorTecnico($query, $tecnicoId)
    {
        return $query->where('tecnico_id', $tecnicoId);
    }

    public function scopePorClinica($query, $clinicaId)
    {
        return $query->where('clinica_id', $clinicaId);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_solicitud', [$fechaInicio, $fechaFin]);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('numero_solicitud', 'like', "%{$termino}%")
              ->orWhere('razon', 'like', "%{$termino}%");
        });
    }

    // Accessors
    public function getEstadoInfoAttribute()
    {
        return self::ESTADOS[$this->estado] ?? self::ESTADOS['pendiente'];
    }

    public function getTotalRepuestosAttribute()
    {
        return $this->repuestos()->count();
    }

    public function getCantidadTotalRepuestosAttribute()
    {
        return $this->repuestos()->sum('cantidad');
    }

    public function getDiasTranscurridosAttribute()
    {
        return $this->fecha_solicitud->diffInDays(now());
    }

    // Boot method para eventos
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($solicitud) {
            if (!$solicitud->numero_solicitud) {
                $solicitud->numero_solicitud = self::generarNumeroSolicitud();
            }
        });
    }

    // Métodos estáticos
    public static function generarNumeroSolicitud()
    {
        $fecha = now()->format('Ymd');
        $ultimo = self::where('numero_solicitud', 'like', "SOL-{$fecha}-%")
                     ->orderBy('numero_solicitud', 'desc')
                     ->first();

        $numero = 1;
        if ($ultimo) {
            $ultimoNumero = intval(substr($ultimo->numero_solicitud, -4));
            $numero = $ultimoNumero + 1;
        }

        return "SOL-{$fecha}-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}