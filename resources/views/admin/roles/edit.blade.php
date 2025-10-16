@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar rol</h1>

        <form action="{{ route('roles.update', $role) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del rol</label>
                <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" id="nombre"
                    value="{{ old('nombre', $role->nombre) }}" required>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Privilegios</label>
                <div class="row">
                    @foreach ($privilegios as $p)
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privilegios[]"
                                    value="{{ $p->id }}" id="priv-{{ $p->id }}"
                                    {{ in_array($p->id, $seleccionados) ? 'checked' : '' }}>
                                <label class="form-check-label" for="priv-{{ $p->id }}">{{ $p->nombre }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button class="btn btn-primary">Actualizar</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection
