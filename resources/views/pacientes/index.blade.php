@extends('layouts.app')

@section('title', 'Pacientes')
@section('page-title', 'Gestión de Pacientes')
@section('breadcrumb', 'Listado de pacientes registrados')

@section('topbar-actions')
    @if(auth()->user()->esAdmin())
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary btn-sm" id="btn-nuevo-paciente">
        <i data-lucide="plus" style="width:16px;height:16px;"></i> Nuevo Paciente
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
        <div class="form-group flex align-center" style="margin:0; min-width:140px; margin-bottom: 12px; gap: 8px;">
            <input type="checkbox" name="archivados" id="archivados" value="1" {{ request()->has('archivados') ? 'checked' : '' }} style="width: 16px; height: 16px;">
            <label for="archivados" class="form-label text-danger" style="margin: 0; cursor: pointer; color: #ef4444; font-weight: 600;">Ver Archivados</label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary"><i data-lucide="search" style="width:14px;height:14px;"></i> Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('pacientes.index') }}" class="btn btn-secondary">Limpiar</a>
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
                                    <div style="font-weight:500;">
                                        {{ $paciente->nombre_completo }}
                                        @if($paciente->trashed())
                                            <span style="background: #fee2e2; color: #ef4444; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; margin-left: 4px; font-weight: bold;">ARCHIVADO</span>
                                        @endif
                                    </div>
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
                                    @if($paciente->trashed())
                                        <form action="{{ route('pacientes.restore', $paciente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea restaurar a este paciente del archivo?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm" style="background:#10b981; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Restaurar</button>
                                        </form>
                                    @else
                                        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-secondary btn-sm">Editar</a>
                                        <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea ELIMINAR (Archivar) a este paciente? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" style="background:#ef4444; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Eliminar</button>
                                        </form>
                                    @endif
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
