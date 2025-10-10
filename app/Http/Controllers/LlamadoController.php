<?php

namespace App\Http\Controllers;

use App\Models\Llamado;
use App\Models\CategoriaLlamado;
use App\Models\CentroMedico;
use App\Models\User;
use Illuminate\Http\Request;

class LlamadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $llamados = Llamado::with(['centroMedico', 'tecnicoAsignado', 'categoriaLlamado'])->get();
        return view('llamados.index', compact('llamados'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $centroMedicos = CentroMedico::all();
        $tecnicos = User::role('Técnico')->get();
        $categorias = CategoriaLlamado::all();

        return view('llamados.create', compact('centroMedicos', 'tecnicos', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_llamado' => 'required|unique:llamados,numero_llamado',
            'fecha_llamado' => 'required|date',
            'hora_llamado' => 'required',
            'centro_medico_id' => 'required|exists:centro_medicos,id',
            'nombre_informante' => 'required',
            'id_equipo' => 'required',
            'desperfecto' => 'required',
            'tecnico_asignado_id' => 'required|exists:users,id',
            'categoria_llamado_id' => 'required|exists:categoria_llamados,id',
        ]);

        Llamado::create($request->all());

        return redirect()->route('llamados.index')
            ->with('success', 'Llamado creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Llamado  $llamado
     * @return \Illuminate\Http\Response
     */
    public function show(Llamado $llamado)
    {
        return view('llamados.show', compact('llamado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Llamado  $llamado
     * @return \Illuminate\Http\Response
     */
    public function edit(Llamado $llamado)
    {
        $centroMedicos = CentroMedico::all();
        $tecnicos = User::role('Técnico')->get();
        $categorias = CategoriaLlamado::all();

        return view('llamados.edit', compact('llamado', 'centroMedicos', 'tecnicos', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Llamado  $llamado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Llamado $llamado)
    {
        $request->validate([
            'numero_llamado' => 'required|unique:llamados,numero_llamado,' . $llamado->id,
            'fecha_llamado' => 'required|date',
            'hora_llamado' => 'required',
            'centro_medico_id' => 'required|exists:centro_medicos,id',
            'nombre_informante' => 'required',
            'id_equipo' => 'required',
            'desperfecto' => 'required',
            'tecnico_asignado_id' => 'required|exists:users,id',
            'categoria_llamado_id' => 'required|exists:categoria_llamados,id',
        ]);

        $llamado->update($request->all());

        return redirect()->route('llamados.index')
            ->with('success', 'Llamado actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Llamado  $llamado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Llamado $llamado)
    {
        $llamado->delete();

        return redirect()->route('llamados.index')
            ->with('success', 'Llamado eliminado exitosamente.');
    }
}
