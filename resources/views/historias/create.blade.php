@extends('layouts.app')

@section('title', 'Nueva Historia Clínica — ' . $paciente->nombre_completo)
@section('page-title', 'Registrar Nueva Visita')
@section('breadcrumb', 'Pacientes / ' . $paciente->nombre_completo . ' / Nueva HC')

@section('topbar-actions')
    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-secondary btn-sm">← Volver al Perfil</a>
@endsection

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <span class="card-title flex align-center gap-2" style="align-items:center;"><i data-lucide="user" style="width:20px;height:20px;"></i> Datos del Paciente</span>
    </div>
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:48px;height:48px;background:linear-gradient(135deg,#4f6ef7,#7c5ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;color:#fff;flex-shrink:0;">
            {{ strtoupper(substr($paciente->nombres,0,1).substr($paciente->apellidos,0,1)) }}
        </div>
        <div>
            <div style="font-weight:600;">{{ $paciente->nombre_completo }}</div>
            <div class="text-muted" style="font-size:.78rem;">
                {{ $paciente->cedula }} ·
                {{ $paciente->fecha_nacimiento?->isoFormat('DD/MM/YYYY') ?? 'S/F' }}
                {{ $paciente->edad ? "({$paciente->edad} años)" : '' }}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Detalles de la Visita (Historia Clínica)</span>
    </div>
    
    <form action="{{ route('historias.store') }}" method="POST">
        @csrf
        <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Fecha de consulta *</label>
                <input type="date" name="fecha_consulta" class="form-control" value="{{ old('fecha_consulta', date('Y-m-d')) }}" required>
            </div>
        </div>

        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.1rem; color: var(--color-primary); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="clipboard-list" style="width:18px;height:18px;"></i> Antecedentes</h4>
        
        <div class="form-group">
            <label class="form-label">Motivo de consulta inicial</label>
            <textarea name="motivo_consulta" class="form-control" rows="2">{{ old('motivo_consulta') }}</textarea>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Grupo sanguíneo</label>
                <input type="text" name="grupo_sanguineo" class="form-control" value="{{ old('grupo_sanguineo') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Medicamentos actuales</label>
                <input type="text" name="medicamentos_actuales" class="form-control" value="{{ old('medicamentos_actuales') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Antecedentes personales</label>
            <textarea name="antecedentes_personales" class="form-control" rows="2">{{ old('antecedentes_personales') }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Antecedentes familiares</label>
            <textarea name="antecedentes_familiares" class="form-control" rows="2">{{ old('antecedentes_familiares') }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Observaciones iniciales</label>
            <textarea name="observaciones_iniciales" class="form-control" rows="2">{{ old('observaciones_iniciales') }}</textarea>
        </div>

        <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.1rem; color: var(--color-primary); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="activity" style="width:18px;height:18px;"></i> Evolución Clínica</h4>

        <div class="form-group">
            <label class="form-label">Evaluación clínica *</label>
            <textarea name="evaluacion" class="form-control" rows="3" required>{{ old('evaluacion') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Tratamiento aplicado *</label>
            <textarea name="tratamiento_aplicado" class="form-control" rows="3" required>{{ old('tratamiento_aplicado') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Respuesta del paciente</label>
            <textarea name="respuesta_paciente" class="form-control" rows="2">{{ old('respuesta_paciente') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Plan para la próxima sesión</label>
            <textarea name="plan_siguiente_sesion" class="form-control" rows="2">{{ old('plan_siguiente_sesion') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Registrar Visita</button>
    </form>
</div>
@endsection
