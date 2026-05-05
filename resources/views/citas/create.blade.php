@extends('layouts.app')

@section('title', 'Nueva Cita')
@section('page-title', 'Registrar Nueva Cita')
@section('breadcrumb', 'Citas / Nueva')

@section('content')
<div class="card" style="max-width:780px;">
    <div class="card-header">
        <span class="card-title">📅 Datos de la cita</span>
        <a href="{{ route('citas.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
    </div>

    <form action="{{ route('citas.store') }}" method="POST" id="form-cita">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="paciente_id">Paciente *</label>
                <select name="paciente_id" id="paciente_id" class="form-select {{ $errors->has('paciente_id') ? 'is-invalid' : '' }}" required>
                    <option value="">— Seleccionar paciente —</option>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" {{ old('paciente_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre_completo }} ({{ $p->cedula }})
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="especialista_id">Especialista *</label>
                <select name="especialista_id" id="especialista_id" class="form-select {{ $errors->has('especialista_id') ? 'is-invalid' : '' }}" required>
                    <option value="">— Seleccionar especialista —</option>
                    @foreach($especialistas as $e)
                        <option value="{{ $e->id }}" {{ old('especialista_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('especialista_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label class="form-label" for="fecha">Fecha *</label>
                <input type="date" name="fecha" id="fecha"
                    class="form-control {{ $errors->has('fecha') ? 'is-invalid' : '' }}"
                    value="{{ old('fecha') }}"
                    min="{{ date('Y-m-d') }}" required>
                @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="hora_inicio">Hora inicio *</label>
                <input type="time" name="hora_inicio" id="hora_inicio"
                    class="form-control {{ $errors->has('hora_inicio') ? 'is-invalid' : '' }}"
                    value="{{ old('hora_inicio') }}" required>
                @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="hora_fin">Hora fin *</label>
                <input type="time" name="hora_fin" id="hora_fin"
                    class="form-control {{ $errors->has('hora_fin') ? 'is-invalid' : '' }}"
                    value="{{ old('hora_fin') }}" required>
                @error('hora_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="motivo">Motivo de la cita</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="3"
                placeholder="Describe brevemente el motivo de la consulta...">{{ old('motivo') }}</textarea>
        </div>

        @if(auth()->user()->esAdmin())
        <div class="form-group">
            <label class="form-label" for="notas_recepcion">Notas de recepción (interno)</label>
            <textarea name="notas_recepcion" id="notas_recepcion" class="form-control" rows="2"
                placeholder="Notas internas de recepción...">{{ old('notas_recepcion') }}</textarea>
        </div>
        @endif

        <hr class="divider">
        <div class="flex-between">
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" id="btn-guardar-cita">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Registrar Cita
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Auto-calcular hora fin (+1 hora) al seleccionar hora inicio
document.getElementById('hora_inicio').addEventListener('change', function() {
    const [h, m] = this.value.split(':').map(Number);
    const fin = new Date(0, 0, 0, h + 1, m);
    document.getElementById('hora_fin').value =
        String(fin.getHours()).padStart(2,'0') + ':' + String(fin.getMinutes()).padStart(2,'0');
});
</script>
@endpush
