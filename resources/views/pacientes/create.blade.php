@extends('layouts.app')

@section('title', 'Nuevo Paciente')
@section('page-title', 'Registrar Paciente')
@section('breadcrumb', 'Pacientes / Nuevo')

@section('content')
<div class="card" style="max-width:860px;">
    <div class="card-header">
        <span class="card-title">👤 Datos del paciente</span>
        <a href="{{ route('pacientes.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
    </div>

    <form action="{{ route('pacientes.store') }}" method="POST" id="form-nuevo-paciente">
        @csrf

        <p class="text-muted mb-2" style="font-size:.82rem;">Los campos marcados con * son obligatorios.</p>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="nombres">Nombres *</label>
                <input type="text" name="nombres" id="nombres"
                    class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                    value="{{ old('nombres') }}" required maxlength="100">
                @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="apellidos">Apellidos *</label>
                <input type="text" name="apellidos" id="apellidos"
                    class="form-control {{ $errors->has('apellidos') ? 'is-invalid' : '' }}"
                    value="{{ old('apellidos') }}" required maxlength="100">
                @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label class="form-label" for="cedula">Cédula *</label>
                <input type="text" name="cedula" id="cedula"
                    class="form-control {{ $errors->has('cedula') ? 'is-invalid' : '' }}"
                    value="{{ old('cedula') }}" required placeholder="V-12345678">
                @error('cedula')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                    class="form-control {{ $errors->has('fecha_nacimiento') ? 'is-invalid' : '' }}"
                    value="{{ old('fecha_nacimiento') }}" max="{{ date('Y-m-d') }}">
                @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="sexo">Sexo</label>
                <select name="sexo" id="sexo" class="form-select">
                    <option value="">— Seleccionar —</option>
                    <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="Otro" {{ old('sexo') === 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono"
                    class="form-control"
                    value="{{ old('telefono') }}" placeholder="0412-000-0000">
            </div>

            <div class="form-group">
                <label class="form-label" for="telefono_emergencia">Teléfono de emergencia</label>
                <input type="text" name="telefono_emergencia" id="telefono_emergencia"
                    class="form-control"
                    value="{{ old('telefono_emergencia') }}" placeholder="0414-000-0000">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input type="email" name="email" id="email"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="ocupacion">Ocupación</label>
                <input type="text" name="ocupacion" id="ocupacion"
                    class="form-control"
                    value="{{ old('ocupacion') }}" maxlength="100">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="direccion">Dirección</label>
            <textarea name="direccion" id="direccion" class="form-control" rows="2"
                placeholder="Urb., Calle, Casa/Apto, Ciudad...">{{ old('direccion') }}</textarea>
        </div>

        <hr class="divider">
        <div class="flex-between">
            <a href="{{ route('pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" id="btn-guardar-paciente">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Registrar Paciente
            </button>
        </div>
    </form>
</div>
@endsection
