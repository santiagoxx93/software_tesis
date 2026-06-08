@extends('layouts.app')

@section('title', 'Nuevo Trabajador')
@section('page-title', 'Registrar Trabajador')
@section('breadcrumb', 'Trabajadores / Nuevo')

@section('content')
<div class="card" style="max-width:860px;">
    <div class="card-header">
        <span class="card-title">🧑‍💼 Datos del trabajador</span>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
    </div>

    <form action="{{ route('usuarios.store') }}" method="POST" id="form-nuevo-usuario">
        @csrf

        <p class="text-muted mb-2" style="font-size:.82rem;">Los campos marcados con * son obligatorios.</p>

        {{-- Datos de acceso --}}
        <p style="font-size:.85rem;font-weight:600;margin-bottom:.75rem;color:var(--color-primary);">📋 Datos de acceso al sistema</p>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="name">Nombre completo *</label>
                <input type="text" name="name" id="name"
                    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}" required maxlength="255"
                    placeholder="Ej: Juan Pérez">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico *</label>
                <input type="email" name="email" id="email"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}" required maxlength="255"
                    placeholder="correo@ejemplo.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="password">Contraseña *</label>
                <input type="password" name="password" id="password"
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    required minlength="6" placeholder="Mínimo 6 caracteres">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar contraseña *</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="form-control"
                    required minlength="6" placeholder="Repita la contraseña">
            </div>
        </div>

        <div class="form-group" style="max-width:400px;">
            <label class="form-label" for="rol">Rol en el sistema *</label>
            <select name="rol" id="rol" class="form-select {{ $errors->has('rol') ? 'is-invalid' : '' }}" required>
                <option value="">— Seleccionar rol —</option>
                <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador (Recepción/Gerencia)</option>
                <option value="especialista" {{ old('rol') === 'especialista' ? 'selected' : '' }}>Especialista (Médico/Terapeuta)</option>
            </select>
            @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <hr class="divider">

        {{-- Datos de especialista (se muestran/ocultan dinámicamente) --}}
        <div id="campos-especialista" style="display:none;">
            <p style="font-size:.85rem;font-weight:600;margin-bottom:.75rem;color:var(--color-success);">🩺 Datos del especialista</p>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="nombres">Nombres *</label>
                    <input type="text" name="nombres" id="nombres"
                        class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                        value="{{ old('nombres') }}" maxlength="100">
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="apellidos">Apellidos *</label>
                    <input type="text" name="apellidos" id="apellidos"
                        class="form-control {{ $errors->has('apellidos') ? 'is-invalid' : '' }}"
                        value="{{ old('apellidos') }}" maxlength="100">
                    @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label" for="cedula">Cédula *</label>
                    <input type="text" name="cedula" id="cedula"
                        class="form-control {{ $errors->has('cedula') ? 'is-invalid' : '' }}"
                        value="{{ old('cedula') }}" placeholder="V-12345678" maxlength="20">
                    @error('cedula')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="especialidad">Especialidad *</label>
                    <input type="text" name="especialidad" id="especialidad"
                        class="form-control {{ $errors->has('especialidad') ? 'is-invalid' : '' }}"
                        value="{{ old('especialidad') }}" placeholder="Ej: Psicología, Nutrición..."
                        maxlength="150">
                    @error('especialidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono"
                        class="form-control"
                        value="{{ old('telefono') }}" placeholder="0412-000-0000" maxlength="20">
                </div>
            </div>
            <hr class="divider" style="margin: 1rem 0;">
            <p style="font-size:.85rem;font-weight:600;margin-bottom:.75rem;color:var(--color-info);">🕒 Horario de Trabajo</p>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="hora_entrada">Hora de Entrada *</label>
                    <input type="time" name="hora_entrada" id="hora_entrada"
                        class="form-control {{ $errors->has('hora_entrada') ? 'is-invalid' : '' }}"
                        value="{{ old('hora_entrada', '08:00') }}">
                    @error('hora_entrada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="hora_salida">Hora de Salida *</label>
                    <input type="time" name="hora_salida" id="hora_salida"
                        class="form-control {{ $errors->has('hora_salida') ? 'is-invalid' : '' }}"
                        value="{{ old('hora_salida', '17:00') }}">
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
                        $oldDias = old('dias_laborables', [1,2,3,4,5]); // Lunes a Viernes por defecto
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
        </div>

        <div class="flex-between">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" id="btn-guardar-usuario">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Registrar Trabajador
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rolSelect = document.getElementById('rol');
        const camposEsp = document.getElementById('campos-especialista');

        function toggleCamposEspecialista() {
            if (rolSelect.value === 'especialista') {
                camposEsp.style.display = 'block';
            } else {
                camposEsp.style.display = 'none';
            }
        }

        rolSelect.addEventListener('change', toggleCamposEspecialista);
        // Ejecutar al cargar para manejar old() values
        toggleCamposEspecialista();
    });
</script>
@endpush
@endsection
