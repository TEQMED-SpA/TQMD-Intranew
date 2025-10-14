<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketAsignado;
use App\Models\TicketHistorial;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('tecnicoAsignado');

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['historial', 'tecnicoAsignado']);

        return view('tickets.show', [
            'ticket' => $ticket,
            'title' => 'Detalle Ticket #' . $ticket->numero_ticket
        ]);
    }

    public function agregarComentario(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
            'foto' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('uploads/tickets', 'public');
        }

        TicketHistorial::create([
            'ticket_id' => $ticket->id,
            'usuario' => Auth::user()->name,
            'rol' => Auth::user()->roles->first()->name ?? 'usuario',
            'accion' => 'Comentario agregado',
            'comentario' => $request->comentario,
            'foto' => $fotoPath,
            'fecha' => now(),
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comentario agregado correctamente');
    }

    public function edit(Ticket $ticket)
    {
        // Solo obtener técnicos (rol_id = 2)
        $tecnicos = User::where('rol_id', 2)
            ->where('activo', 1)
            ->orderBy('name')
            ->get();

        return view('tickets.edit', compact('ticket', 'tecnicos'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,reagendar,completado', // Removido 'en_proceso', agregado 'reagendar'
            'tecnico_asignado_id' => 'nullable|exists:users,id',
            'fecha_visita' => 'nullable|date',
            'acciones_realizadas' => 'nullable|string',
            'motivo_reagendamiento' => 'nullable|string|max:500'
        ]);

        // Guardar valores anteriores para detectar cambios
        $estado_anterior = $ticket->estado;
        $tecnico_anterior_id = $ticket->tecnico_asignado_id;
        $fecha_anterior = $ticket->fecha_visita;
        $tecnico_anterior_nombre = $tecnico_anterior_id ? User::find($tecnico_anterior_id)->name : null;

        // Detectar si hubo reagendamiento
        $fecha_nueva = $request->fecha_visita ? Carbon::parse($request->fecha_visita) : null;
        $hubo_reagendamiento = false;
        $hubo_cambio_fecha = false;

        // Verificar cambios en la fecha
        if ($fecha_anterior && $fecha_nueva && !$fecha_anterior->equalTo($fecha_nueva)) {
            $hubo_cambio_fecha = true;
        }

        // Verificar si se seleccionó estado "reagendar" o hubo cambio de fecha
        if ($request->estado === 'reagendar' || $hubo_cambio_fecha) {
            $hubo_reagendamiento = true;
        }

        // Preparar datos para actualización
        $updateData = [
            'estado' => $request->estado === 'reagendar' ? 'pendiente' : $request->estado, // Convertir reagendar a pendiente
            'tecnico_asignado_id' => $request->tecnico_asignado_id,
            'fecha_visita' => $fecha_nueva,
            'acciones_realizadas' => $request->acciones_realizadas,
        ];

        // Actualizar el ticket
        $ticket->update($updateData);

        // Registrar cambio de estado en historial (si cambió y no es reagendamiento)
        if ($estado_anterior !== $updateData['estado'] && !$hubo_reagendamiento) {
            TicketHistorial::create([
                'ticket_id' => $ticket->id,
                'usuario' => Auth::user()->name,
                'rol' => 'admin',
                'accion' => 'Cambio de estado',
                'estado_anterior' => $estado_anterior,
                'estado_nuevo' => $updateData['estado'],
                'fecha' => now(),
            ]);
        }

        // Registrar reagendamiento en historial
        if ($hubo_reagendamiento) {
            $comentario_reagendamiento = "Fecha anterior: " .
                ($fecha_anterior ? $fecha_anterior->format('d/m/Y H:i') : 'Sin programar') .
                " → Nueva fecha: " .
                ($fecha_nueva ? $fecha_nueva->format('d/m/Y H:i') : 'Sin programar');

            if ($request->motivo_reagendamiento) {
                $comentario_reagendamiento .= "\nMotivo: " . $request->motivo_reagendamiento;
            }

            TicketHistorial::create([
                'ticket_id' => $ticket->id,
                'usuario' => Auth::user()->name,
                'rol' => 'admin',
                'accion' => 'Ticket reagendado',
                'comentario' => $comentario_reagendamiento,
                'fecha' => now(),
            ]);
        }

        // Registrar cambio de técnico en historial
        if ($tecnico_anterior_id !== $request->tecnico_asignado_id) {
            $tecnico_nuevo_nombre = $request->tecnico_asignado_id ? User::find($request->tecnico_asignado_id)->name : null;

            TicketHistorial::create([
                'ticket_id' => $ticket->id,
                'usuario' => Auth::user()->name,
                'rol' => 'admin',
                'accion' => 'Cambio de técnico asignado',
                'tecnico_anterior' => $tecnico_anterior_nombre,
                'tecnico_nuevo' => $tecnico_nuevo_nombre,
                'fecha' => now(),
            ]);

            // Notificar al nuevo técnico por correo
            if ($request->tecnico_asignado_id) {
                $tecnico = User::find($request->tecnico_asignado_id);
                if ($tecnico && $tecnico->email) {
                    Mail::to($tecnico->email)->send(new TicketAsignado($ticket));
                }
            }
        }

        // Mensaje de éxito personalizado
        $mensaje = 'Ticket actualizado correctamente.';
        if ($hubo_reagendamiento) {
            $mensaje = 'Ticket reagendado correctamente. El cambio ha sido registrado en el historial.';
        } elseif ($tecnico_anterior_id !== $request->tecnico_asignado_id && $request->tecnico_asignado_id) {
            $mensaje .= ' El técnico asignado fue notificado por correo.';
        }

        return redirect()->route('tickets.index')->with('success', $mensaje);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado correctamente');
    }

    public function getEvidenciaUrlAttribute($filename)
    {
        return "https://llamados.teqmed.cl/uploads/tickets/" . $filename;
    }

    /**
     * Método para cambiar un ticket a "en proceso" solo cuando el técnico comience a trabajar
     */
    public function iniciarTrabajo(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comentario_inicio' => 'nullable|string|max:500'
        ]);

        $estado_anterior = $ticket->estado;
        $ticket->update(['estado' => 'en_proceso']);

        // Registrar inicio de trabajo en historial
        TicketHistorial::create([
            'ticket_id' => $ticket->id,
            'usuario' => Auth::user()->name,
            'rol' => 'tecnico',
            'accion' => 'Inicio de trabajo técnico',
            'estado_anterior' => $estado_anterior,
            'estado_nuevo' => 'en_proceso',
            'comentario' => $request->comentario_inicio ?: 'El técnico ha comenzado a trabajar en el ticket',
            'fecha' => now(),
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket marcado como "En Proceso". ¡Buen trabajo!');
    }
}
