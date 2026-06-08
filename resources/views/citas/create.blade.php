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
                        @php
                            $diasMap = [1=>'Lun', 2=>'Mar', 3=>'Mié', 4=>'Jue', 5=>'Vie', 6=>'Sáb', 7=>'Dom'];
                            $diasE = $e->dias_laborables ?? [1,2,3,4,5];
                            $diasTexto = implode(', ', array_map(fn($d) => $diasMap[$d] ?? '', $diasE));
                            $hEntrada = $e->hora_entrada ? date('h:i A', strtotime($e->hora_entrada)) : '08:00 AM';
                            $hSalida = $e->hora_salida ? date('h:i A', strtotime($e->hora_salida)) : '05:00 PM';
                            $infoHorario = "Días: $diasTexto | Horario: $hEntrada — $hSalida";
                        @endphp
                        <option value="{{ $e->id }}" data-horario="{{ $infoHorario }}" {{ old('especialista_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->nombre_completo }} — {{ $e->especialidad ?? 'Sin especialidad' }}
                        </option>
                    @endforeach
                </select>
                <div id="info-horario-especialista" class="text-info mt-1" style="display:none; font-size: 0.75rem; background: rgba(56, 189, 248, 0.1); padding: 6px 10px; border-radius: 6px; border-left: 3px solid var(--color-info); margin-top: 8px;">
                    🕒 <strong style="font-weight:600;">Disponibilidad:</strong> <span id="texto-horario"></span>
                </div>
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
    if (!this.value) return;
    const [h, m] = this.value.split(':').map(Number);
    const fin = new Date(0, 0, 0, h + 1, m);
    const finStr = String(fin.getHours()).padStart(2,'0') + ':' + String(fin.getMinutes()).padStart(2,'0');
    
    const horaFinEl = document.getElementById('hora_fin');
    if (horaFinEl._flatpickr) {
        horaFinEl._flatpickr.setDate(finStr, true); // true para trigger change events
    } else {
        horaFinEl.value = finStr;
    }
});

// Mostrar horario del especialista seleccionado
const espSelect = document.getElementById('especialista_id');
const infoBox = document.getElementById('info-horario-especialista');
const textoBox = document.getElementById('texto-horario');

function updateHorario() {
    if(espSelect.selectedIndex < 0) return;
    const selected = espSelect.options[espSelect.selectedIndex];
    const horario = selected.getAttribute('data-horario');
    if (horario && espSelect.value !== '') {
        textoBox.textContent = horario;
        infoBox.style.display = 'block';
    } else {
        infoBox.style.display = 'none';
    }
}
espSelect.addEventListener('change', updateHorario);
updateHorario(); // Ejecutar al cargar la página
</script>
@endpush
