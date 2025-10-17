<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Privilegio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('privilegios')
            ->withCount([
                'users as usuarios_count',
                'privilegios as permisos_count',
            ])
            ->orderBy('nombre')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $todos = Privilegio::orderBy('nombre')->get();
        $privilegiosPorModulo = $this->groupPrivilegios($todos);

        return view('admin.roles.create', compact('privilegiosPorModulo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'unique:roles,nombre'],
            'privilegios' => ['array'],
            'privilegios.*' => ['exists:privilegios,id'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $fields = ['nombre' => $data['nombre']];

        if (Schema::hasColumn('roles', 'descripcion')) {
            $fields['descripcion'] = $request->input('descripcion');
        }
        if (Schema::hasColumn('roles', 'activo')) {
            $fields['activo'] = $request->boolean('activo');
        }

        $role = Role::create($fields);
        $role->privilegios()->sync($data['privilegios'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Rol creado correctamente.');
    }

    public function show(Role $role)
    {
        $rol = $role->load(['privilegios', 'users'])->loadCount('users as usuarios_count');
        // Agrupa SOLO los privilegios de este rol
        $privilegiosPorModulo = $this->groupPrivilegios($rol->privilegios);

        return view('admin.roles.show', compact('rol', 'privilegiosPorModulo'));
    }

    public function edit(Role $role)
    {
        $rol = $role->load('privilegios')->loadCount('users as usuarios_count');
        $todos = Privilegio::orderBy('nombre')->get();
        $privilegiosPorModulo = $this->groupPrivilegios($todos);
        $privilegiosAsignados = $rol->privilegios()->pluck('privilegios.id')->toArray();

        return view('admin.roles.edit', compact('rol', 'privilegiosPorModulo', 'privilegiosAsignados'));
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
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $updates = ['nombre' => $data['nombre']];

        if (Schema::hasColumn('roles', 'descripcion')) {
            $updates['descripcion'] = $request->input('descripcion');
        }
        if (Schema::hasColumn('roles', 'activo')) {
            $updates['activo'] = $request->boolean('activo');
        }

        // Evita actualizar columnas que no existen
        if (!empty($updates)) {
            $role->update($updates);
        }

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

    /**
     * Agrupa privilegios por módulo si existe la columna `modulo` en la tabla.
     * Si no existe, entrega un único grupo "general".
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection  $privilegios
     * @return \Illuminate\Support\Collection
     */
    private function groupPrivilegios($privilegios): Collection
    {
        if (Schema::hasColumn('privilegios', 'modulo')) {
            return $privilegios->groupBy(function ($p) {
                return $p->modulo ?: 'general';
            });
        }

        return collect(['general' => $privilegios]);
    }
}
