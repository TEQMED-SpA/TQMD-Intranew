<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InformeCorrectivo extends Model
{
    use HasFactory;

    protected $table = 'informes_correctivos';

    protected $fillable = [
        'numero_folio',
        'centro_medico_id',
        'equipo_id',
        'cliente_id',
        'fecha_servicio',
        'fecha_notificacion',
        'problema_informado',
        'hora_inicio',
        'hora_cierre',
        'trabajo_realizado',
        'condicion_equipo',
        'usuario_id',
        'firma',
        'firma_cliente',
        'firma_cliente_nombre',
    ];

    protected $casts = [
        'fecha_servicio'     => 'date',
        'fecha_notificacion' => 'date',
        // Si solo guardas "HH:MM:SS" en la BD puedes dejarlos como string
        'hora_inicio'        => 'datetime:H:i',
        'hora_cierre'        => 'datetime:H:i',
    ];

    // Relaciones
    public function centroMedico(): BelongsTo
    {
        return $this->belongsTo(CentroMedico::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function repuestos(): HasMany
    {
        return $this->hasMany(RegRepuestoInforme::class, 'informe_correctivo_id');
    }
}
