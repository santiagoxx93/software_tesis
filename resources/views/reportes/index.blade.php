@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')
@section('page-title', 'Reportes y Estadísticas')
@section('breadcrumb', 'Métricas del Centro San Alfonso')

@section('topbar-actions')
    <a href="{{ route('reportes.pdf', request()->all()) }}" class="btn btn-primary btn-sm" target="_blank">
        <i data-lucide="printer" style="width:16px;height:16px;"></i> Exportar a PDF
    </a>
@endsection

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
        <div class="stat-icon" style="background: rgba(56,189,248,.15); color: #38bdf8;"><i data-lucide="activity"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Visitas Atendidas</span>
            <span class="stat-value">{{ $historias->count() }}</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(34,197,94,.15); color: #22c55e;"><i data-lucide="check-circle"></i></div>
        <div class="stat-info">
            <span class="stat-label">Citas Programadas</span>
            <span class="stat-value">{{ $totalCitas }}</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(239,68,68,.15); color: #ef4444;"><i data-lucide="alert-triangle"></i></div>
        <div class="stat-info">
            <span class="stat-label">Ausencias</span>
            <span class="stat-value">{{ $ausencias }}</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(168,85,247,.15); color: #a855f7;"><i data-lucide="user-plus"></i></div>
        <div class="stat-info">
            <span class="stat-label">Pacientes de Nuevo Ingreso</span>
            <span class="stat-value">{{ $nuevosPacientes }}</span>
        </div>
    </div>
</div>

<div class="grid-2 mb-4">
    {{-- Grafico Pacientes Atendidos (Diario) --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Pacientes Atendidos por Día</span>
        </div>
        <div style="position: relative; height:300px;">
            <canvas id="chartAtendidosDiarios"></canvas>
        </div>
    </div>

    {{-- Grafico Atenciones por Especialista --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Atenciones por Especialista en el Periodo</span>
        </div>
        <div style="position: relative; height:300px;">
            <canvas id="chartEspecialistas"></canvas>
        </div>
    </div>
</div>

{{-- Tablas de Resumen --}}
<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Resumen Semanal de Visitas</span>
        </div>
        <div class="table-wrap">
            <table style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th>Semana (Lunes)</th>
                        <th style="text-align:right;">Pacientes Atendidos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($atendidosSemanales as $semana => $visitas)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($semana)->locale('es')->isoFormat('DD MMM YYYY') }} (Semana)</td>
                        <td style="text-align:right; font-weight:600;">{{ $visitas->count() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-muted text-center">No hay visitas en este periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">Resumen Mensual de Visitas</span>
        </div>
        <div class="table-wrap">
            <table style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th style="text-align:right;">Pacientes Atendidos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($atendidosMensuales as $mes => $visitas)
                    <tr>
                        <td style="text-transform: capitalize;">{{ \Carbon\Carbon::parse($mes)->locale('es')->isoFormat('MMMM YYYY') }}</td>
                        <td style="text-align:right; font-weight:600;">{{ $visitas->count() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-muted text-center">No hay visitas en este periodo.</td>
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
.stat-label { font-size: .75rem; color: var(--color-text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }
.stat-value { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
.ml-2 { margin-left: 0.5rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para Gráfico Diario
        const labelsDiario = {!! json_encode($atendidosDiarios->keys()) !!};
        const dataDiario = {!! json_encode($atendidosDiarios->map->count()->values()) !!};

        new Chart(document.getElementById('chartAtendidosDiarios'), {
            type: 'line',
            data: {
                labels: labelsDiario,
                datasets: [{
                    label: 'Visitas Atendidas',
                    data: dataDiario,
                    borderColor: '#4f6ef7',
                    backgroundColor: 'rgba(79, 110, 247, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f6ef7',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#4f6ef7',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 17, 23, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#e2e6f3',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        titleFont: { size: 14, family: 'Inter' },
                        bodyFont: { size: 13, family: 'Inter' }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Datos para Gráfico Especialistas
        const labelsEsp = {!! json_encode($atencionesPorEspecialista->keys()) !!};
        const dataEsp = {!! json_encode($atencionesPorEspecialista->values()) !!};

        new Chart(document.getElementById('chartEspecialistas'), {
            type: 'bar',
            data: {
                labels: labelsEsp,
                datasets: [{
                    label: 'Visitas',
                    data: dataEsp,
                    backgroundColor: ['#38bdf8', '#22c55e', '#a855f7', '#f59e0b', '#ef4444'],
                    hoverBackgroundColor: ['#1ea7e3', '#16a34a', '#9333ea', '#d97706', '#dc2626'],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 17, 23, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#e2e6f3',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        titleFont: { size: 14, family: 'Inter' },
                        bodyFont: { size: 13, family: 'Inter' }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@endpush
