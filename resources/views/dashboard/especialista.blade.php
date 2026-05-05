@extends('layouts.app')

@section('title', 'Dashboard — Especialista')
@section('page-title', 'Mi Panel')
@section('breadcrumb', 'Bienvenido/a, ' . auth()->user()->name)

@section('content')
@php
    $especialista = auth()->user()->especialista;
    $hoy = \Carbon\Carbon::today();
    $citasHoy = \App\Models\Cita::where('especialista_id', $especialista?->id)
        ->whereDate('fecha', $hoy)
        ->with('paciente')
        ->orderBy('hora_inicio')
        ->get();
    $totalPacientes = \App\Models\Cita::where('especialista_id', $especialista?->id)
        ->distinct('paciente_id')->count('paciente_id');
    $citasSemana = \App\Models\Cita::where('especialista_id', $especialista?->id)
        ->whereBetween('fecha', [$hoy->startOfWeek(), $hoy->copy()->endOfWeek()])
        ->count();
    $pendientes = \App\Models\Cita::where('especialista_id', $especialista?->id)
        ->where('estado', 'pendiente')->count();
@endphp

<div class="grid-3 mb-3">
    <div class="stat-card">
        <div class="stat-icon blue">📅</div>
        <div>
            <div class="stat-value">{{ $citasHoy->count() }}</div>
            <div class="stat-label">Mis citas hoy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">📋</div>
        <div>
            <div class="stat-value">{{ $citasSemana }}</div>
            <div class="stat-label">Esta semana</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">👥</div>
        <div>
            <div class="stat-value">{{ $totalPacientes }}</div>
            <div class="stat-label">Pacientes atendidos</div>
        </div>
    </div>
</div>

<div class="grid-2">
    {{-- Mis citas de hoy --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🗓️ Mis citas de hoy</span>
            <a href="{{ route('citas.index') }}" class="btn btn-secondary btn-sm">Ver todas</a>
        </div>
        @if($citasHoy->isEmpty())
            <p class="text-muted">No tienes citas programadas para hoy.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($citasHoy as $cita)
                        <tr>
                            <td><strong>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</strong></td>
                            <td>{{ $cita->paciente->nombre_completo }}</td>
                            <td><span class="badge badge-{{ $cita->estado }}">{{ ucfirst($cita->estado) }}</span></td>
                            <td>
                                @if($cita->paciente->historiaClinica)
                                <a href="{{ route('historias.show', $cita->paciente->historiaClinica) }}"
                                   class="btn btn-primary btn-sm">Ver HC</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Acceso rápido a pacientes --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">👤 Acceso rápido</span>
            <a href="{{ route('pacientes.index') }}" class="btn btn-secondary btn-sm">Ver todos</a>
        </div>
        @php
            $ultimosPacientes = \App\Models\Cita::where('especialista_id', $especialista?->id)
                ->with('paciente')
                ->orderByDesc('fecha')
                ->get()
                ->unique('paciente_id')
                ->take(8);
        @endphp
        @if($ultimosPacientes->isEmpty())
            <p class="text-muted">No tienes pacientes atendidos aún.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: .5rem;">
                @foreach($ultimosPacientes as $c)
                <a href="{{ $c->paciente->historiaClinica ? route('historias.show', $c->paciente->historiaClinica) : route('pacientes.show', $c->paciente) }}"
                   style="display:flex;align-items:center;gap:.75rem;padding:.6rem .75rem;border-radius:8px;background:var(--color-surface-2);border:1px solid var(--color-border);text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='var(--color-border)'"
                   onmouseout="this.style.background='var(--color-surface-2)'">
                    <div style="width:32px;height:32px;background:linear-gradient(135deg,#4f6ef7,#7c5ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr($c->paciente->nombres, 0, 1) . substr($c->paciente->apellidos, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:.85rem;font-weight:500;color:var(--color-text);">{{ $c->paciente->nombre_completo }}</div>
                        <div style="font-size:.72rem;color:var(--color-text-muted);">{{ $c->paciente->cedula }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
