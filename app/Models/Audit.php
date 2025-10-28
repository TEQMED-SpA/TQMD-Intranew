<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $table = 'audits';

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'action',
        'before_changes',
        'after_changes',
        'ip',
        'user_agent'
    ];

    const UPDATED_AT = null; // Solo usamos created_at

    protected $casts = [
        'before_changes' => 'array',
        'after_changes' => 'array',
        'created_at' => 'datetime'
    ];

    // Relación con el usuario que realizó la acción
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método para obtener la entidad relacionada
    public function auditable()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }

    // Método para obtener una descripción legible de los cambios
    public function getChangeDescriptionAttribute()
    {
        $description = [];

        if ($this->before_changes && $this->after_changes) {
            foreach ($this->after_changes as $field => $newValue) {
                $oldValue = $this->before_changes[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $description[] = "Campo '$field' cambió de '$oldValue' a '$newValue'";
                }
            }
        } elseif ($this->after_changes) {
            foreach ($this->after_changes as $field => $value) {
                $description[] = "Campo '$field' establecido a '$value'";
            }
        }

        return implode('. ', $description);
    }

    // Scope para filtrar por tipo de entidad
    public function scopeForEntity($query, $entityType, $entityId = null)
    {
        $query->where('entity_type', $entityType);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        return $query;
    }

    // Scope para filtrar por usuario
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope para filtrar por acción
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Scope para filtrar por fecha
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
