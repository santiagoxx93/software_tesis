@extends('layouts.app')

@section('title', $paciente->nombre_completo . ' — Perfil')
@section('page-title', $paciente->nombre_completo)
@section('breadcrumb', 'Pacientes / Perfil del paciente')

@section('topbar-actions')
    @if(auth()->user()->esAdmin())
        <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary btn-sm">
            + Nueva Cita
        </a>
        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-secondary btn-sm">Editar datos</a>
    @endif
    @if(auth()->user()->esEspecialista() && $paciente->historiaClinica)
        <a href="{{ route('historias.show', $paciente->historiaClinica) }}" class="btn btn-primary btn-sm">
            📋 Ver Historia Clínica
        </a>
    @endif
@endsection

@section('content')

<div class="grid-2" style="align-items:start;">
    {{-- Datos personales --}}
    <div>
        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title">👤 Datos personales</span>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#4f6ef7,#7c5ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr($paciente->nombres,0,1).substr($paciente->apellidos,0,1)) }}
                </div>
                <div>
                    <div style="font-size:1.1rem;font-weight:700;">{{ $paciente->nombre_completo }}</div>
                    <div class="text-muted">{{ $paciente->cedula }}</div>
                </div>
            </div>
            <table style="width:100%;">
                <tbody>
                    <tr><td class="text-muted" style="padding:.4rem .6rem;width:40%;">Fecha de nac.</td><td style="padding:.4rem .6rem;">{{ $paciente->fecha_nacimiento?->isoFormat('DD/MM/YYYY') ?? '—' }} {{ $paciente->edad ? "({$paciente->edad} años)" : '' }}</td></tr>
                    <tr><td class="text-muted" style="padding:.4rem .6rem;">Sexo</td><td style="padding:.4rem .6rem;">{{ ['M'=>'Masculino','F'=>'Femenino','Otro'=>'Otro'][$paciente->sexo] ?? '—' }}</td></tr>
                    <tr><td class="text-muted" style="padding:.4rem .6rem;">Ocupación</td><td style="padding:.4rem .6rem;">{{ $paciente->ocupacion ?? '—' }}</td></tr>
                    <tr><td class="text-muted" style="padding:.4rem .6rem;">Teléfono</td><td style="padding:.4rem .6rem;">{{ $paciente->telefono ?? '—' }}</td></tr>
                    <tr><td class="text-muted" style="padding:.4rem .6rem;">T. Emergencia</td><td style="padding:.4rem .6rem;">{{ $paciente->telefono_emergencia ?? '—' }}</td></tr>
                    <tr><td class="text-muted" style="padding:.4rem .6rem;">Correo</td><td style="padding:.4rem .6rem;">{{ $paciente->email ?? '—' }}</td></tr>
                    <tr><td class="text-muted" style="padding:.4rem .6rem;">Dirección</td><td style="padding:.4rem .6rem;">{{ $paciente->direccion ?? '—' }}</td></tr>
                </tbody>
            </table>
        </div>

        {{-- Últimas citas (accesible por todos) --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📅 Últimas citas</span>
                <a href="{{ route('citas.index') }}" class="btn btn-secondary btn-sm">Ver todas</a>
            </div>
            @if($paciente->citas->isEmpty())
                <p class="text-muted">Sin citas registradas.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Fecha</th><th>Especialista</th><th>Estado</th></tr>
                        </thead>
                        <tbody>
                            @foreach($paciente->citas as $cita)
                            <tr>
                                <td>{{ $cita->fecha->isoFormat('DD/MM/YYYY') }}</td>
                                <td>{{ $cita->especialista->nombre_completo }}</td>
                                <td><span class="badge badge-{{ $cita->estado }}">{{ ucfirst($cita->estado) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Historia Clínica (solo especialista) --}}
    @if(auth()->user()->esEspecialista() && $paciente->historiaClinica)
    <div class="card">
        <div class="card-header">
            <span class="card-title">🩺 Resumen Historia Clínica</span>
            <a href="{{ route('historias.show', $paciente->historiaClinica) }}" class="btn btn-primary btn-sm">Ver completa</a>
        </div>
        <p class="text-muted" style="font-size:.8rem;margin-bottom:1rem;">
            Solo visible para especialistas — Secreto Médico.
        </p>
        <table style="width:100%;">
            <tbody>
                <tr>
                    <td class="text-muted" style="padding:.4rem .6rem;width:45%;vertical-align:top;">Motivo consulta</td>
                    <td style="padding:.4rem .6rem;">{{ $paciente->historiaClinica->motivo_consulta ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="text-muted" style="padding:.4rem .6rem;vertical-align:top;">Ant. personales</td>
                    <td style="padding:.4rem .6rem;">{{ $paciente->historiaClinica->antecedentes_personales ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="text-muted" style="padding:.4rem .6rem;vertical-align:top;">Grupo sanguíneo</td>
                    <td style="padding:.4rem .6rem;">{{ $paciente->historiaClinica->grupo_sanguineo ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="text-muted" style="padding:.4rem .6rem;vertical-align:top;">Evoluciones</td>
                    <td style="padding:.4rem .6rem;font-weight:600;">
                        {{ $paciente->historiaClinica->evoluciones->count() }} sesiones registradas
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @elseif(auth()->user()->esAdmin())
    <div class="card">
        <div class="card-header">
            <span class="card-title">🔒 Historia Clínica</span>
        </div>
        <div class="alert alert-warning" style="margin:0;">
            La historia clínica es de acceso exclusivo para los especialistas (Secreto Médico).
        </div>
    </div>
    @endif
</div>
@endsection
