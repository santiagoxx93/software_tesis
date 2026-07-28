@extends('layouts.app')

@section('title', 'Historia Clínica — ' . $historia->paciente->nombre_completo)
@section('page-title', 'Historia Clínica (Visita: ' . $historia->fecha_consulta?->isoFormat('DD/MM/YYYY') . ')')
@section('breadcrumb', 'Pacientes / ' . $historia->paciente->nombre_completo . ' / HC')

@section('topbar-actions')
    <a href="{{ route('pacientes.show', $historia->paciente) }}" class="btn btn-secondary btn-sm">← Volver al Perfil</a>
@endsection

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <span class="card-title flex align-center gap-2" style="align-items:center;"><i data-lucide="user" style="width:20px;height:20px;"></i> Datos del Paciente</span>
    </div>
    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
        <div style="width:48px;height:48px;background:linear-gradient(135deg,#4f6ef7,#7c5ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;color:#fff;flex-shrink:0;">
            {{ strtoupper(substr($historia->paciente->nombres,0,1).substr($historia->paciente->apellidos,0,1)) }}
        </div>
        <div>
            <div style="font-weight:600;">{{ $historia->paciente->nombre_completo }}</div>
            <div class="text-muted" style="font-size:.78rem;">
                {{ $historia->paciente->cedula }} ·
                {{ $historia->paciente->fecha_nacimiento?->isoFormat('DD/MM/YYYY') ?? 'S/F' }}
                {{ $historia->paciente->edad ? "({$historia->paciente->edad} años)" : '' }}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header flex-between">
        <span class="card-title">Detalles de la Visita</span>
        @if($historia->estaBloqueado())
            <span class="badge bg-secondary"><i data-lucide="lock" style="width:12px;height:12px;margin-bottom:-2px;"></i> Bloqueado</span>
        @endif
    </div>
    <form action="{{ route('historias.update', $historia) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Fecha de consulta *</label>
                <input type="date" name="fecha_consulta" class="form-control" value="{{ old('fecha_consulta', $historia->fecha_consulta?->format('Y-m-d')) }}" required {{ $historia->estaBloqueado() ? 'disabled' : '' }}>
            </div>
            <div class="form-group">
                <label class="form-label">Especialista</label>
                <input type="text" class="form-control" value="{{ $historia->especialista->nombre_completo ?? 'N/A' }}" disabled>
            </div>
        </div>

        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.1rem; color: var(--color-primary); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="clipboard-list" style="width:18px;height:18px;"></i> Antecedentes</h4>
        
        <div class="form-group">
            <label class="form-label">Motivo de consulta inicial</label>
            <textarea name="motivo_consulta" class="form-control" rows="2" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('motivo_consulta', $historia->motivo_consulta) }}</textarea>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Grupo sanguíneo</label>
                <input type="text" name="grupo_sanguineo" class="form-control" value="{{ old('grupo_sanguineo', $historia->grupo_sanguineo) }}" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>
            </div>
            <div class="form-group">
                <label class="form-label">Medicamentos actuales</label>
                <input type="text" name="medicamentos_actuales" class="form-control" value="{{ old('medicamentos_actuales', $historia->medicamentos_actuales) }}" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Antecedentes personales</label>
            <textarea name="antecedentes_personales" class="form-control" rows="2" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('antecedentes_personales', $historia->antecedentes_personales) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Antecedentes familiares</label>
            <textarea name="antecedentes_familiares" class="form-control" rows="2" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('antecedentes_familiares', $historia->antecedentes_familiares) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Observaciones iniciales</label>
            <textarea name="observaciones_iniciales" class="form-control" rows="2" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('observaciones_iniciales', $historia->observaciones_iniciales) }}</textarea>
        </div>

        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.1rem; color: var(--color-primary); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="activity" style="width:18px;height:18px;"></i> Evolución Clínica</h4>

        <div class="form-group">
            <label class="form-label">Evaluación clínica *</label>
            <textarea name="evaluacion" class="form-control" rows="3" required {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('evaluacion', $historia->evaluacion) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Tratamiento aplicado *</label>
            <textarea name="tratamiento_aplicado" class="form-control" rows="3" required {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('tratamiento_aplicado', $historia->tratamiento_aplicado) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Respuesta del paciente</label>
            <textarea name="respuesta_paciente" class="form-control" rows="2" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('respuesta_paciente', $historia->respuesta_paciente) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Plan para la próxima sesión</label>
            <textarea name="plan_siguiente_sesion" class="form-control" rows="2" {{ $historia->estaBloqueado() ? 'disabled' : '' }}>{{ old('plan_siguiente_sesion', $historia->plan_siguiente_sesion) }}</textarea>
        </div>

        @if(!$historia->estaBloqueado())
            <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
        @endif
    </form>
</div>
@endsection
