@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')
@section('page-title', 'Reportes y Estadísticas')
@section('breadcrumb', 'Métricas del Centro San Alfonso')

@section('content')

{{-- Filtro de Rango de Fechas --}}
<div class="card mb-4" style="background: var(--color-surface-2);">
    <form action="{{ route('reportes.index') }}" method="GET" class="flex gap-3 align-end" style="flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group" style="margin:0; min-width:180px;">
            <label class="form-label">Desde</label>
            <input type="date" name="start_date" class="form-control" value="{{ $start->toDateString() }}">
        </div>
        <div class="form-group" style="margin:0; min-width:180px;">
            <label class="form-label">Hasta</label>
            <input type="date" name="end_date" class="form-control" value="{{ $end->toDateString() }}">
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Generar Reporte</button>
            <a href="{{ route('reportes.index') }}" class="btn btn-secondary ml-2">Mes Actual</a>
        </div>
    </form>
</div>

<h3 class="mb-3" style="font-size: 1.1rem; font-weight: 600;">
    Resultados del {{ $start->isoFormat('D MMM YYYY') }} al {{ $end->isoFormat('D MMM YYYY') }}
</h3>

{{-- KPIs Principales --}}
<div class="grid-4 mb-4">
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(56,189,248,.15); color: #38bdf8;">📅</div>
        <div class="stat-info">
            <span class="stat-label">Total de Citas</span>
            <span class="stat-value">{{ $totalCitas }}</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(34,197,94,.15); color: #22c55e;">✅</div>
        <div class="stat-info">
            <span class="stat-label">Completadas</span>
            <span class="stat-value">{{ $completadas }}</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(239,68,68,.15); color: #ef4444;">🚨</div>
        <div class="stat-info">
            <span class="stat-label">Ausencias</span>
            <span class="stat-value">{{ $ausencias }}</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(168,85,247,.15); color: #a855f7;">👤</div>
        <div class="stat-info">
            <span class="stat-label">Nuevos Pacientes</span>
            <span class="stat-value">{{ $nuevosPacientes }}</span>
        </div>
    </div>
</div>

<div class="grid-2">
    {{-- Tasa de Asistencia --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Tasa de Asistencia</span>
        </div>
        <div class="flex" style="align-items:center; justify-content:center; padding: 2rem;">
            <div class="attendance-circle {{ $tasaAsistencia >= 80 ? 'good' : ($tasaAsistencia >= 50 ? 'warning' : 'danger') }}">
                <span>{{ $tasaAsistencia }}%</span>
            </div>
        </div>
        <p class="text-center text-muted" style="font-size:.85rem;">
            Basado en citas completadas vs ausentes.
        </p>
    </div>

    {{-- Citas por Especialista --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Citas por Especialista</span>
        </div>
        <div class="table-wrap">
            <table style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th>Especialista</th>
                        <th style="text-align:right;">Citas Asignadas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citasPorEspecialista as $nombre => $cantidad)
                    <tr>
                        <td>{{ $nombre }}</td>
                        <td style="text-align:right; font-weight:600;">{{ $cantidad }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-muted text-center">No hay citas en este periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
}
.stat-info { display: flex; flex-direction: column; }
.stat-label { font-size: .8rem; color: var(--color-text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }
.stat-value { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }

/* Attendance Circle indicator */
.attendance-circle {
    width: 140px; height: 140px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 700;
    box-shadow: inset 0 0 0 12px var(--color-border);
    position: relative;
}
.attendance-circle.good { color: #22c55e; box-shadow: inset 0 0 0 12px rgba(34,197,94,.2); border: 2px solid #22c55e; }
.attendance-circle.warning { color: #f59e0b; box-shadow: inset 0 0 0 12px rgba(245,158,11,.2); border: 2px solid #f59e0b; }
.attendance-circle.danger { color: #ef4444; box-shadow: inset 0 0 0 12px rgba(239,68,68,.2); border: 2px solid #ef4444; }

.ml-2 { margin-left: 0.5rem; }
</style>
@endpush
