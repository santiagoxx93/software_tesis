@extends('layouts.app')

@section('title', 'Editar Cita')
@section('page-title', 'Editar Cita')
@section('breadcrumb', 'Citas / Editar')

@section('content')
<div class="card" style="max-width:780px;">
    <div class="card-header">
        <span class="card-title">✏️ Editar cita</span>
        <a href="{{ route('citas.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
    </div>

    <form action="{{ route('citas.update', $cita) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Paciente *</label>
                <select name="paciente_id" class="form-select {{ $errors->has('paciente_id') ? 'is-invalid' : '' }}" required>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" {{ old('paciente_id', $cita->paciente_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre_completo }} ({{ $p->cedula }})
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Especialista *</label>
                <select name="especialista_id" class="form-select {{ $errors->has('especialista_id') ? 'is-invalid' : '' }}" required>
                    @foreach($especialistas as $e)
                        <option value="{{ $e->id }}" {{ old('especialista_id', $cita->especialista_id) == $e->id ? 'selected' : '' }}>
                            {{ $e->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('especialista_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label class="form-label">Fecha *</label>
                <input type="date" name="fecha"
                    class="form-control {{ $errors->has('fecha') ? 'is-invalid' : '' }}"
                    value="{{ old('fecha', $cita->fecha->toDateString()) }}" required>
                @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Hora inicio *</label>
                <input type="time" name="hora_inicio"
                    class="form-control {{ $errors->has('hora_inicio') ? 'is-invalid' : '' }}"
                    value="{{ old('hora_inicio', substr($cita->hora_inicio, 0, 5)) }}" required>
                @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Hora fin *</label>
                <input type="time" name="hora_fin"
                    class="form-control {{ $errors->has('hora_fin') ? 'is-invalid' : '' }}"
                    value="{{ old('hora_fin', substr($cita->hora_fin, 0, 5)) }}" required>
                @error('hora_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Motivo</label>
            <textarea name="motivo" class="form-control" rows="3">{{ old('motivo', $cita->motivo) }}</textarea>
        </div>

        {{-- Cambio de estado --}}
        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                @foreach(['pendiente','confirmada','completada','cancelada','ausente','reprogramada'] as $e)
                    <option value="{{ $e }}" {{ old('estado', $cita->estado) === $e ? 'selected' : '' }}>
                        {{ ucfirst($e) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="motivo-cancelacion-box" style="{{ $cita->estado === 'cancelada' ? '' : 'display:none;' }}">
            <div class="form-group">
                <label class="form-label">Motivo de cancelación</label>
                <textarea name="motivo_cancelacion" class="form-control" rows="2">{{ old('motivo_cancelacion', $cita->motivo_cancelacion) }}</textarea>
            </div>
        </div>

        <hr class="divider">
        <div class="flex-between">
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const estadoSelect = document.querySelector('select[name="estado"]');
const motivoBox = document.getElementById('motivo-cancelacion-box');
estadoSelect.addEventListener('change', () => {
    motivoBox.style.display = estadoSelect.value === 'cancelada' ? '' : 'none';
});
</script>
@endpush
