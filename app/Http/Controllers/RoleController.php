<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Privilegio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('privilegios')->orderBy('nombre')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $privilegios = Privilegio::orderBy('nombre')->get();
        return view('admin.roles.create', compact('privilegios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'unique:roles,nombre'],
            'privilegios' => ['array'],
            'privilegios.*' => ['exists:privilegios,id'],
        ]);

        $role = Role::create(['nombre' => $data['nombre']]);
        $role->privilegios()->sync($data['privilegios'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        $privilegios = Privilegio::orderBy('nombre')->get();
        $seleccionados = $role->privilegios()->pluck('privilegios.id')->toArray();

        return view('admin.roles.edit', compact('role', 'privilegios', 'seleccionados'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:80',
                Rule::unique('roles', 'nombre')->ignore($role->id),
            ],
            'privilegios' => ['array'],
            'privilegios.*' => ['exists:privilegios,id'],
        ]);

        $role->update(['nombre' => $data['nombre']]);
        $role->privilegios()->sync($data['privilegios'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->nombre === 'admin') {
            return back()->with('error', 'No se puede eliminar el rol admin.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'No se puede eliminar un rol con usuarios asignados.');
        }

        $role->privilegios()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('status', 'Rol eliminado.');
    }
}
