<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_ticket',
        'cliente',
        'nombre_apellido',
        'telefono',
        'cargo',
        'email',
        'estado',
        'id_numero_equipo',
        'modelo_maquina',
        'falla_presentada',
        'momento_falla',
        'momento_falla_otras',
        'acciones_realizadas',
        'estado',
        'llamado_id',
        'tecnico_asignado_id',
        'fecha_visita',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'fecha_visita' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relación con el técnico asignado
    public function tecnicoAsignado()
    {
        return $this->belongsTo(User::class, 'tecnico_asignado_id');
    }

    // Relación con llamado
    public function llamado()
    {
        return $this->belongsTo(Llamado::class, 'llamado_id');
    }

    // Scope para buscar
    public function scopeBuscar($query, $buscar)
    {
        return $query->where('numero_ticket', 'like', "%{$buscar}%")
            ->orWhere('cliente', 'like', "%{$buscar}%")
            ->orWhere('nombre_apellido', 'like', "%{$buscar}%")
            ->orWhere('telefono', 'like', "%{$buscar}%");
    }

    // Relación con el historial

    public function historial()
    {
        return $this->hasMany(TicketHistorial::class)->orderBy('fecha', 'desc');
    }

    /**
     * Accessor para URL de evidencia (cualquier tipo de archivo)
     */
    public function getEvidenciaUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }

        // Verificar si es una URL completa
        if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
            return $this->foto;
        }

        // Construir URL completa
        return "https://llamados.teqmed.cl/uploads/tickets/" . $this->foto;
    }

    /**
     * Verificar si tiene evidencia
     */
    public function hasEvidencia()
    {
        return !empty($this->foto);
    }

    /**
     * Obtener el tipo de archivo
     */
    public function getTipoEvidenciaAttribute()
    {
        if (!$this->foto) {
            return 'none';
        }

        $extension = strtolower(pathinfo($this->foto, PATHINFO_EXTENSION));

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'document';
        } else {
            return 'file';
        }
    }

    /**
     * Verificar si es imagen
     */
    public function esImagen()
    {
        return $this->tipo_evidencia === 'image';
    }

    /**
     * Verificar si es video
     */
    public function esVideo()
    {
        return $this->tipo_evidencia === 'video';
    }

    /**
     * Verificar si es documento
     */
    public function esDocumento()
    {
        return $this->tipo_evidencia === 'document';
    }
}
