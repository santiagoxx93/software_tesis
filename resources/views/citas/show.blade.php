@extends('layouts.app')

@section('title', 'Detalle de la Cita')
@section('page-title', 'Información de la Cita')
@section('breadcrumb')
    <a href="{{ route('citas.index') }}" style="color:var(--color-primary); text-decoration:none;">Citas</a> / Detalle
@endsection

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span class="card-title" style="margin: 0;">Cita #{{ $cita->id }}</span>
        <span class="badge badge-{{ $cita->estado }}">
            {{ ucfirst($cita->estado) }}
        </span>
    </div>

    <div style="padding: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px;">
            {{-- Columna 1: Información General y Paciente --}}
            <div>
                <h4 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--color-border); color: var(--color-text);">
                    📅 Información General
                </h4>
                <div style="margin-bottom: 8px;">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </div>
                <div style="margin-bottom: 24px;">
                    <strong>Horario:</strong> {{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('h:i A') }}
                </div>
                
                <h4 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--color-border); color: var(--color-text);">
                    👤 Paciente
                </h4>
                <div style="margin-bottom: 8px;">
                    <strong>Nombre:</strong> 
                    <a href="{{ route('pacientes.show', $cita->paciente) }}" style="color:var(--color-primary); font-weight:500;">
                        {{ $cita->paciente->nombre_completo }}
                    </a>
                </div>
                <div style="margin-bottom: 8px;"><strong>Cédula:</strong> {{ $cita->paciente->cedula }}</div>
                <div><strong>Teléfono:</strong> {{ $cita->paciente->telefono ?? 'N/A' }}</div>
            </div>

            {{-- Columna 2: Especialista y Datos Administrativos --}}
            <div>
                <h4 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--color-border); color: var(--color-text);">
                    🩺 Especialista
                </h4>
                <div style="margin-bottom: 8px;"><strong>Nombre:</strong> {{ $cita->especialista->nombre_completo }}</div>
                <div style="margin-bottom: 24px;"><strong>Especialidad:</strong> {{ $cita->especialista->especialidad }}</div>

                <h4 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--color-border); color: var(--color-text);">
                    📋 Datos Administrativos
                </h4>
                <div style="margin-bottom: 12px;"><strong>Registrada por:</strong> {{ $cita->registradoPor->name ?? 'Sistema' }}</div>
                
                <div style="margin-bottom: 8px;"><strong>Motivo de consulta:</strong></div>
                <div style="background-color: var(--color-bg); padding: 12px; border-radius: 6px; border: 1px solid var(--color-border); font-size: 0.95rem; min-height: 60px;">
                    {{ $cita->motivo ?: 'Sin motivo especificado.' }}
                </div>
            </div>
        </div>

        {{-- Alertas y Cancelaciones --}}
        @if($cita->estado === 'cancelada')
            <div style="margin-top: 32px; background-color: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: 16px; border-radius: 4px;">
                <h4 style="font-weight: 600; color: #ef4444; margin-bottom: 8px;">Información de Cancelación</h4>
                <div style="color: #b91c1c; margin-bottom: 4px;"><strong>Cancelada por:</strong> {{ $cita->canceladoPor->name ?? 'N/A' }}</div>
                <div style="color: #b91c1c;"><strong>Motivo:</strong> {{ $cita->motivo_cancelacion }}</div>
            </div>
        @elseif($cita->estado === 'reprogramada' && $cita->citaReprogramada)
            <div style="margin-top: 32px; background-color: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6; padding: 16px; border-radius: 4px;">
                <h4 style="font-weight: 600; color: #3b82f6; margin-bottom: 8px;">Cita Reprogramada</h4>
                <div style="color: #1d4ed8;">
                    Esta cita fue reprogramada. Puedes ver la nueva cita aquí:
                    <a href="{{ route('citas.show', $cita->cita_reprogramada_id) }}" style="font-weight: 600; text-decoration: underline;">Ver Cita #{{ $cita->cita_reprogramada_id }}</a>
                </p>
            </div>
        @endif
        
        {{-- Notas de Recepción (solo admin) --}}
        @if(auth()->user()->esAdmin() && $cita->notas_recepcion)
            <div style="margin-top: 32px; background-color: rgba(234, 179, 8, 0.1); border-left: 4px solid #eab308; padding: 16px; border-radius: 4px;">
                <h4 style="font-weight: 600; color: #a16207; margin-bottom: 8px;">Notas de Recepción (Uso Interno)</h4>
                <div style="color: #854d0e;">{{ $cita->notas_recepcion }}</div>
            </div>
        @endif

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--color-border); display: flex; gap: 12px;">
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">Volver a la Agenda</a>
            @if(in_array($cita->estado, ['pendiente', 'confirmada']))
                <a href="{{ route('citas.edit', $cita) }}" class="btn btn-primary">Editar Cita</a>
            @endif
        </div>
    </div>
</div>
@endsection
