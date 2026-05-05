@extends('layouts.app')

@section('title', 'Pacientes')
@section('page-title', 'Gestión de Pacientes')
@section('breadcrumb', 'Listado de pacientes registrados')

@section('topbar-actions')
    @if(auth()->user()->esAdmin())
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary btn-sm" id="btn-nuevo-paciente">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Paciente
    </a>
    @endif
@endsection

@section('content')

{{-- Buscador --}}
<div class="card mb-3">
    <form action="{{ route('pacientes.index') }}" method="GET" class="flex gap-3" style="align-items:flex-end;">
        <div class="form-group" style="margin:0;flex:1;">
            <label class="form-label">Buscar paciente</label>
            <input type="text" name="buscar" class="form-control"
                placeholder="Nombre, apellido o cédula..."
                value="{{ request('buscar') }}"
                id="input-buscar-paciente">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('pacientes.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Pacientes ({{ $pacientes->total() }})</span>
    </div>

    @if($pacientes->isEmpty())
        <p class="text-muted">No se encontraron pacientes.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Edad</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pacientes as $paciente)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:34px;height:34px;background:linear-gradient(135deg,#4f6ef7,#7c5ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($paciente->nombres,0,1).substr($paciente->apellidos,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500;">{{ $paciente->nombre_completo }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">{{ $paciente->ocupacion ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $paciente->cedula }}</td>
                        <td>{{ $paciente->edad ?? '—' }} años</td>
                        <td>{{ $paciente->telefono ?? '—' }}</td>
                        <td>{{ $paciente->email ?? '—' }}</td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-secondary btn-sm">Ver</a>
                                @if(auth()->user()->esAdmin())
                                <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-secondary btn-sm">Editar</a>
                                @endif
                                @if(auth()->user()->esEspecialista() && $paciente->historiaClinica)
                                <a href="{{ route('historias.show', $paciente->historiaClinica) }}" class="btn btn-primary btn-sm">HC</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{ $pacientes->links() }}
        </div>
    @endif
</div>
@endsection
