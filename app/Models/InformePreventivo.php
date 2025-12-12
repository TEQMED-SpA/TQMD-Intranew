<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InformePreventivo extends Model
{
    use HasFactory;

    protected $table = 'informes_preventivos';

    protected $fillable = [
        'tipo_informe_preventivo_id',
        'fecha',
        'usuario_id',
        'centro_medico_id',
        'equipo_id',
        'numero_reporte_servicio',
        'tipo_trabajo',
        'condicion_equipo',
        'horas_operacion',
        'comentarios',
        'fecha_proximo_control',
        'firma_tecnico',
        'firma_cliente',
        'firma_cliente_nombre',
    ];

    protected $casts = [
        'fecha'                => 'date',
        'fecha_proximo_control' => 'date',
    ];

    // ───────────────────────────────────────────────
    //   Relaciones
    // ───────────────────────────────────────────────

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function centroMedico(): BelongsTo
    {
        return $this->belongsTo(CentroMedico::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function tipoInformePreventivo(): BelongsTo
    {
        return $this->belongsTo(TipoInformePreventivo::class, 'tipo_informe_preventivo_id');
    }

    public function inspecciones(): HasMany
    {
        return $this->hasMany(InformePreventivoInspeccion::class, 'informe_preventivo_id');
    }

    public function repuestos(): HasMany
    {
        return $this->hasMany(RegRepuestoInforme::class, 'informe_preventivo_id');
    }
}
