<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Producto;
use App\Models\User;
use App\Models\CentroMedico;
use Illuminate\Http\Request;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        $query = Salida::with(['producto', 'usuarioPedido', 'usuarioRequiere', 'centroMedico']);
        $salidas = $query->orderBy('fecha_hora', 'desc')->paginate(15);
        return view('salidas.index', compact('salidas'));
    }

    public function create()
    {
        $productos = Producto::orderBy('producto_nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $centros = CentroMedico::orderBy('centro_dialisis')->get();
        return view('salidas.create', compact('productos', 'usuarios', 'centros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'solicitud_id' => 'nullable|exists:solicitudes,id',
            'producto_id' => 'required|exists:producto,producto_id',
            'usuario_pedido_id' => 'required|exists:users,id',
            'usuario_requiere_id' => 'required|exists:users,id',
            'cantidad' => 'required|integer|min:1',
            'centro_medico_id' => 'required|exists:centros_medicos,id',
            'fecha_hora' => 'required|date'
        ]);
        Salida::create($request->all());
        return redirect()->route('salidas.index')->with('success', 'Salida registrada correctamente');
    }

    public function show(Salida $salida)
    {
        return view('salidas.show', compact('salida'));
    }

    public function edit(Salida $salida)
    {
        $productos = Producto::orderBy('producto_nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $centros = CentroMedico::orderBy('centro_dialisis')->get();
        return view('salidas.edit', compact('salida', 'productos', 'usuarios', 'centros'));
    }

    public function update(Request $request, Salida $salida)
    {
        $request->validate([
            'solicitud_id' => 'nullable|exists:solicitudes,id',
            'producto_id' => 'required|exists:producto,producto_id',
            'usuario_pedido_id' => 'required|exists:users,id',
            'usuario_requiere_id' => 'required|exists:users,id',
            'cantidad' => 'required|integer|min:1',
            'centro_medico_id' => 'required|exists:centros_medicos,id',
            'fecha_hora' => 'required|date'
        ]);
        $salida->update($request->all());
        return redirect()->route('salidas.show', $salida)->with('success', 'Salida actualizada correctamente');
    }

    public function destroy(Salida $salida)
    {
        $salida->delete();
        return redirect()->route('salidas.index')->with('success', 'Salida eliminada correctamente');
    }
}
