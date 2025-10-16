@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Roles</h1>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Nuevo rol</a>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Privilegios</th>
                    <th>Usuarios</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td>{{ $role->nombre }}</td>
                        <td>
                            @if ($role->privilegios->isEmpty())
                                <span class="text-muted">Sin privilegios</span>
                            @else
                                <ul class="mb-0">
                                    @foreach ($role->privilegios as $p)
                                        <li>{{ $p->nombre }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td>{{ $role->users()->count() }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar rol?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    {{ $role->nombre === 'admin' ? 'disabled' : '' }}>Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No hay roles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
