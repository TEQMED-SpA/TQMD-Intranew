<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketAsignado;

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
        $ticket->load('tecnicoAsignado');
        return view('tickets.show', compact('ticket'));
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
            'estado' => 'required|in:pendiente,en_proceso,completado',
            'tecnico_asignado_id' => 'nullable|exists:users,id',
            'fecha_visita' => 'nullable|date',
            'acciones_realizadas' => 'nullable|string',
        ]);

        // Detectar si el técnico cambia
        $tecnico_anterior_id = $ticket->tecnico_asignado_id;

        $ticket->update($request->only([
            'estado',
            'tecnico_asignado_id',
            'fecha_visita',
            'acciones_realizadas'
        ]));

        // Notificar al técnico sólo si ha cambiado o si antes era null
        if (
            $ticket->tecnico_asignado_id
            && $ticket->tecnico_asignado_id != $tecnico_anterior_id
        ) {
            $tecnico = User::find($ticket->tecnico_asignado_id);
            if ($tecnico && $tecnico->email) {
                Mail::to($tecnico->email)->send(new TicketAsignado($ticket));
            }
        }

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket actualizado correctamente. Si cambió el técnico, fue notificado por correo.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado correctamente');
    }
}
