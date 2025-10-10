<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('rol')->orderBy('name')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('nombre')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'rol_id'   => 'nullable|exists:roles,id',
            'avatar'   => 'nullable|string|max:255',
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['activo'] = 1; // Activo por defecto

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('nombre')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'rol_id'   => 'nullable|exists:roles,id',
            'avatar'   => 'nullable|string|max:255',
            'activo'   => 'required|boolean',
            'password' => 'nullable|string|min:6|confirmed'
        ]);
        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        return redirect()->route('users.show', $user)->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        $user->update(['activo' => 0]);
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
    }
}
