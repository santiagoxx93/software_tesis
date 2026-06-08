@extends('layouts.app')

@section('title', 'Editar Trabajador')
@section('page-title', 'Editar Trabajador')
@section('breadcrumb', 'Trabajadores / Editar')

@section('content')
<div class="card" style="max-width:860px;">
    <div class="card-header">
        <span class="card-title">✏️ Editar trabajador: {{ $usuario->name }}</span>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
    </div>

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST" id="form-editar-usuario">
        @csrf
        @method('PUT')

        <p class="text-muted mb-2" style="font-size:.82rem;">Los campos marcados con * son obligatorios. Deje la contraseña en blanco si no desea cambiarla.</p>

        {{-- Datos de acceso --}}
        <p style="font-size:.85rem;font-weight:600;margin-bottom:.75rem;color:var(--color-primary);">📋 Datos de acceso al sistema</p>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="name">Nombre completo *</label>
                <input type="text" name="name" id="name"
                    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name', $usuario->name) }}" required maxlength="255">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico *</label>
                <input type="email" name="email" id="email"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email', $usuario->email) }}" required maxlength="255">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="password">Nueva contraseña <span class="text-muted">(dejar en blanco para no cambiar)</span></label>
                <input type="password" name="password" id="password"
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    minlength="6" placeholder="••••••••">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="form-control"
                    minlength="6" placeholder="••••••••">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Rol asignado</label>
                <input type="text" class="form-control" disabled
                    value="{{ $usuario->hasRole('admin') ? 'Administrador' : ($usuario->hasRole('especialista') ? 'Especialista' : 'Sin rol') }}"
                    style="opacity:.7;">
                <small class="text-muted" style="font-size:.72rem;">El rol no se puede cambiar después de crear el usuario.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="activo">Estado *</label>
                <select name="activo" id="activo" class="form-select" required>
                    <option value="1" {{ old('activo', $usuario->activo) ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('activo', $usuario->activo) ? '' : 'selected' }}>Inactivo</option>
                </select>
            </div>
        </div>

        @if($usuario->hasRole('especialista') && $usuario->especialista)
        <hr class="divider">

        <p style="font-size:.85rem;font-weight:600;margin-bottom:.75rem;color:var(--color-success);">🩺 Datos del especialista</p>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="nombres">Nombres *</label>
                <input type="text" name="nombres" id="nombres"
                    class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                    value="{{ old('nombres', $usuario->especialista->nombres) }}" maxlength="100">
                @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="apellidos">Apellidos *</label>
                <input type="text" name="apellidos" id="apellidos"
                    class="form-control {{ $errors->has('apellidos') ? 'is-invalid' : '' }}"
                    value="{{ old('apellidos', $usuario->especialista->apellidos) }}" maxlength="100">
                @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label class="form-label" for="cedula">Cédula *</label>
                <input type="text" name="cedula" id="cedula"
                    class="form-control {{ $errors->has('cedula') ? 'is-invalid' : '' }}"
                    value="{{ old('cedula', $usuario->especialista->cedula) }}" maxlength="20">
                @error('cedula')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="especialidad">Especialidad *</label>
                <input type="text" name="especialidad" id="especialidad"
                    class="form-control {{ $errors->has('especialidad') ? 'is-invalid' : '' }}"
                    value="{{ old('especialidad', $usuario->especialista->especialidad) }}" maxlength="150">
                @error('especialidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono"
                    class="form-control"
                    value="{{ old('telefono', $usuario->especialista->telefono) }}" maxlength="20">
            </div>
        </div>
        
        <hr class="divider" style="margin: 1rem 0;">
        <p style="font-size:.85rem;font-weight:600;margin-bottom:.75rem;color:var(--color-info);">🕒 Horario de Trabajo</p>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="hora_entrada">Hora de Entrada *</label>
                <input type="time" name="hora_entrada" id="hora_entrada"
                    class="form-control {{ $errors->has('hora_entrada') ? 'is-invalid' : '' }}"
                    value="{{ old('hora_entrada', substr($usuario->especialista->hora_entrada, 0, 5)) }}">
                @error('hora_entrada')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="hora_salida">Hora de Salida *</label>
                <input type="time" name="hora_salida" id="hora_salida"
                    class="form-control {{ $errors->has('hora_salida') ? 'is-invalid' : '' }}"
                    value="{{ old('hora_salida', substr($usuario->especialista->hora_salida, 0, 5)) }}">
                @error('hora_salida')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Días Laborables *</label>
            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:.5rem;">
                @php
                    $dias = [
                        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
                        4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
                    ];
                    $oldDias = old('dias_laborables', $usuario->especialista->dias_laborables ?? [1,2,3,4,5]);
                @endphp
                @foreach($dias as $num => $nombre)
                    <label style="display:flex; align-items:center; gap:.4rem; font-size:.85rem; cursor:pointer;">
                        <input type="checkbox" name="dias_laborables[]" value="{{ $num }}"
                            {{ in_array($num, $oldDias) ? 'checked' : '' }}>
                        {{ $nombre }}
                    </label>
                @endforeach
            </div>
            @error('dias_laborables')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
        </div>
        @endif

        <hr class="divider">

        <div class="flex-between">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" id="btn-actualizar-usuario">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
