<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\TicketHistorial;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        TicketHistorial::create([
            'ticket_id' => $ticket->id,
            'usuario' => Auth::user()->name ?? 'Sistema',
            'rol' => $this->getUserRole(),
            'accion' => 'Creación de ticket',
            'estado_nuevo' => $ticket->estado,
            'tecnico_nuevo' => $ticket->tecnicoAsignado->name ?? null,
            'comentario' => 'Ticket creado',
            'fecha' => now(),
        ]);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        $cambios = $ticket->getChanges();
        $original = $ticket->getOriginal();

        if (empty($cambios)) {
            return;
        }

        $accion = 'Actualización de ticket';
        $comentario = [];

        // Detectar cambios en el estado
        $estadoAnterior = isset($cambios['estado']) ? $original['estado'] : null;
        $estadoNuevo = $cambios['estado'] ?? null;

        // Detectar cambios en el técnico
        $tecnicoAnterior = null;
        $tecnicoNuevo = null;

        if (isset($cambios['tecnico_asignado_id'])) {
            $tecnicoAnterior = $original['tecnico_asignado_id']
                ? optional(\App\Models\User::find($original['tecnico_asignado_id']))->name
                : null;
            $tecnicoNuevo = optional($ticket->tecnicoAsignado)->name;

            if ($tecnicoAnterior || $tecnicoNuevo) {
                $comentario[] = "Técnico cambiado de '" . ($tecnicoAnterior ?? 'Sin asignar') . "' a '" . ($tecnicoNuevo ?? 'Sin asignar') . "'";
            }
        }

        if ($estadoNuevo) {
            $comentario[] = "Estado cambiado de '{$estadoAnterior}' a '{$estadoNuevo}'";
        }

        // Solo crear el historial si hay cambios significativos
        if (!empty($comentario) || $estadoNuevo || isset($cambios['tecnico_asignado_id'])) {
            TicketHistorial::create([
                'ticket_id' => $ticket->id,
                'usuario' => Auth::user()->name ?? 'Sistema',
                'rol' => $this->getUserRole(),
                'accion' => $accion,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'tecnico_anterior' => $tecnicoAnterior,
                'tecnico_nuevo' => $tecnicoNuevo,
                'comentario' => !empty($comentario) ? implode('. ', $comentario) : null,
                'fecha' => now(),
            ]);
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        TicketHistorial::create([
            'ticket_id' => $ticket->id,
            'usuario' => Auth::user()->name ?? 'Sistema',
            'rol' => $this->getUserRole(),
            'accion' => 'Eliminación de ticket',
            'comentario' => 'Ticket eliminado',
            'fecha' => now(),
        ]);
    }

    /**
     * Obtener el rol del usuario de forma segura
     */
    private function getUserRole(): string
    {
        if (!Auth::check()) {
            return 'sistema';
        }

        $user = Auth::user();

        // Si el usuario tiene roles
        if ($user->roles && $user->roles->count() > 0) {
            return $user->roles->first()->name;
        }

        // Si no tiene roles, usar un rol por defecto
        return 'usuario';
    }
}
