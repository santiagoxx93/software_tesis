@extends('layouts.app')

@section('title', 'Editar Paciente — ' . $paciente->nombre_completo)
@section('page-title', 'Editar Paciente')
@section('breadcrumb', 'Pacientes / ' . $paciente->nombre_completo . ' / Editar')

@section('content')
<div class="card" style="max-width:860px;">
    <div class="card-header">
        <span class="card-title">✏️ Editar: {{ $paciente->nombre_completo }}</span>
        <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-secondary btn-sm">← Volver</a>
    </div>

    <form action="{{ route('pacientes.update', $paciente) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Nombres *</label>
                <input type="text" name="nombres" class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                    value="{{ old('nombres', $paciente->nombres) }}" required maxlength="100">
                @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control {{ $errors->has('apellidos') ? 'is-invalid' : '' }}"
                    value="{{ old('apellidos', $paciente->apellidos) }}" required maxlength="100">
                @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label class="form-label">Cédula *</label>
                <input type="text" name="cedula" class="form-control {{ $errors->has('cedula') ? 'is-invalid' : '' }}"
                    value="{{ old('cedula', $paciente->cedula) }}" required>
                @error('cedula')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control"
                    value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento?->toDateString()) }}"
                    max="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-select">
                    <option value="">— Seleccionar —</option>
                    <option value="M" {{ old('sexo', $paciente->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo', $paciente->sexo) === 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="Otro" {{ old('sexo', $paciente->sexo) === 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control"
                    value="{{ old('telefono', $paciente->telefono) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono de emergencia</label>
                <input type="text" name="telefono_emergencia" class="form-control"
                    value="{{ old('telefono_emergencia', $paciente->telefono_emergencia) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email', $paciente->email) }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Ocupación</label>
                <input type="text" name="ocupacion" class="form-control"
                    value="{{ old('ocupacion', $paciente->ocupacion) }}" maxlength="100">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Dirección</label>
            <textarea name="direccion" class="form-control" rows="2">{{ old('direccion', $paciente->direccion) }}</textarea>
        </div>

        <hr class="divider">
        <div class="flex-between">
            <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
