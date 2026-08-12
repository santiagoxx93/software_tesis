@extends('layouts.app')

@section('title', 'Mis Pacientes (Historias Clínicas)')
@section('page-title', 'Buscador de Pacientes')
@section('breadcrumb', 'Historias Clínicas / Buscar Paciente')

@section('content')

<div class="card mb-3">
    <div class="card-header" style="border-bottom:none;">
        <span class="card-title">Listado de Pacientes ({{ $pacientes->total() }})</span>
    </div>
    
    <div style="padding: 0 1.5rem 1rem;">
        <form action="{{ route('historias.index') }}" method="GET" style="display:flex; gap: 0.5rem; max-width: 400px;">
            <div style="position:relative; flex:1;">
                <i data-lucide="search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); width:16px; color:var(--color-text-muted);"></i>
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por cédula, nombre o apellido..." value="{{ request('buscar') }}" style="padding-left: 32px;">
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
            @if(request()->filled('buscar'))
                <a href="{{ route('historias.index') }}" class="btn btn-secondary">Limpiar</a>
            @endif
        </form>
        <p class="text-muted" style="margin-top: 1rem; font-size: 0.9rem;">
            Busca y selecciona un paciente para ver su línea de tiempo de evoluciones clínicas o registrar una nueva visita.
        </p>
    </div>

    @if($pacientes->isEmpty())
        <div style="padding: 0 1.5rem 1.5rem;">
            <p class="text-muted">No se encontraron pacientes registrados.</p>
        </div>
    @else
        <div class="table-wrap" style="padding: 0 1.5rem 1.5rem;">
            <table>
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Paciente</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pacientes as $paciente)
                    <tr>
                        <td style="font-weight: 500;">{{ $paciente->cedula }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:34px;height:34px;background:linear-gradient(135deg,#7c5ef7,#4f6ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($paciente->nombres,0,1).substr($paciente->apellidos,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500;">{{ $paciente->nombre_completo }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">{{ $paciente->ocupacion ?? 'Sin ocupación' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($paciente->telefono)
                                <i data-lucide="phone" style="width:14px;height:14px;margin-bottom:-2px;color:var(--color-text-muted);"></i> {{ $paciente->telefono }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('historias.paciente', $paciente) }}" class="btn btn-primary btn-sm"><i data-lucide="clock" style="width:14px;height:14px;margin-right:4px;"></i> Ver Historial</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2" style="padding: 0 1.5rem 1.5rem;">
            {{ $pacientes->links() }}
        </div>
    @endif
</div>
@endsection
