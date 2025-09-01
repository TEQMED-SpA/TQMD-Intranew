<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\CentroMedico;
use App\Models\User;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $query = Solicitud::with(['tecnico', 'clinica']);
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        $solicitudes = $query->orderBy('fecha_solicitud', 'desc')->paginate(15);
        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $clinicas = CentroMedico::orderBy('centro_dialisis')->get();
        $tecnicos = User::orderBy('name')->get();
        return view('solicitudes.create', compact('clinicas', 'tecnicos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_solicitud' => 'required|string|max:255|unique:solicitudes',
            'fecha_solicitud' => 'required|date',
            'tecnico_id' => 'required|exists:users,id',
            'clinica_id' => 'required|exists:centros_medicos,id',
            'razon' => 'required|string',
            'estado' => 'required|string|max:255'
        ]);
        Solicitud::create($request->all());
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada correctamente');
    }

    public function show(Solicitud $solicitud)
    {
        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        $clinicas = CentroMedico::orderBy('centro_dialisis')->get();
        $tecnicos = User::orderBy('name')->get();
        return view('solicitudes.edit', compact('solicitud', 'clinicas', 'tecnicos'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'numero_solicitud' => 'required|string|max:255|unique:solicitudes,numero_solicitud,' . $solicitud->id,
            'fecha_solicitud' => 'required|date',
            'tecnico_id' => 'required|exists:users,id',
            'clinica_id' => 'required|exists:centros_medicos,id',
            'razon' => 'required|string',
            'estado' => 'required|string|max:255'
        ]);
        $solicitud->update($request->all());
        return redirect()->route('solicitudes.show', $solicitud)->with('success', 'Solicitud actualizada correctamente');
    }

    public function destroy(Solicitud $solicitud)
    {
        $solicitud->delete();
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada correctamente');
    }
}
