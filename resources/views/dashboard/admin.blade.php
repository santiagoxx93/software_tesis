@extends('layouts.app')

@section('title', 'Dashboard — Administración')
@section('page-title', 'Panel de Control')
@section('breadcrumb', 'Bienvenido, ' . auth()->user()->name)

@section('topbar-actions')
    <a href="{{ route('citas.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Cita
    </a>
@endsection

@section('content')
@php
    $hoy        = \Carbon\Carbon::today();
    $citasHoy   = \App\Models\Cita::whereDate('fecha', $hoy)->with('paciente','especialista')->orderBy('hora_inicio')->get();
    $totalMes   = \App\Models\Cita::whereMonth('fecha', $hoy->month)->whereYear('fecha', $hoy->year)->count();
    $ausentes   = \App\Models\Cita::whereMonth('fecha', $hoy->month)->where('estado','ausente')->count();
    $pacientes  = \App\Models\Paciente::count();
@endphp

{{-- Estadísticas rápidas --}}
<div class="grid-4 mb-3">
    <div class="stat-card">
        <div class="stat-icon blue">📅</div>
        <div>
            <div class="stat-value">{{ $citasHoy->count() }}</div>
            <div class="stat-label">Citas hoy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">📋</div>
        <div>
            <div class="stat-value">{{ $totalMes }}</div>
            <div class="stat-label">Citas este mes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">⚠️</div>
        <div>
            <div class="stat-value">{{ $ausentes }}</div>
            <div class="stat-label">Ausentes este mes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div>
            <div class="stat-value">{{ $pacientes }}</div>
            <div class="stat-label">Pacientes registrados</div>
        </div>
    </div>
</div>

{{-- Citas del día --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📅 Agenda de hoy — {{ $hoy->isoFormat('dddd, D [de] MMMM') }}</span>
        <a href="{{ route('citas.index', ['fecha' => $hoy->toDateString()]) }}" class="btn btn-secondary btn-sm">Ver todas</a>
    </div>

    @if($citasHoy->isEmpty())
        <p class="text-muted">No hay citas programadas para hoy.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Especialista</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citasHoy as $cita)
                    <tr>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</strong>
                            <span class="text-muted"> — {{ \Carbon\Carbon::parse($cita->hora_fin)->format('h:i A') }}</span>
                        </td>
                        <td>{{ $cita->paciente->nombre_completo }}</td>
                        <td>{{ $cita->especialista->nombre_completo }}</td>
                        <td>
                            <span class="badge badge-{{ $cita->estado }}">
                                {{ ucfirst($cita->estado) }}
                            </span>
                        </td>
                        <td class="flex gap-2">
                            @if(in_array($cita->estado, ['pendiente','confirmada']))
                            <form action="{{ route('citas.cambiarEstado', $cita) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="confirmada">
                                <button class="btn btn-success btn-sm">✓ Confirmar</button>
                            </form>
                            <form action="{{ route('citas.cambiarEstado', $cita) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="ausente">
                                <button class="btn btn-danger btn-sm">✗ Ausente</button>
                            </form>
                            @endif
                            <a href="{{ route('citas.edit', $cita) }}" class="btn btn-secondary btn-sm">Editar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
