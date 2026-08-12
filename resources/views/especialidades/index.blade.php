@extends('layouts.app')

@section('title', 'Especialidades')
@section('page-title', 'Gestión de Especialidades Médicas')
@section('breadcrumb', 'Catálogo de especialidades del sistema')

@section('topbar-actions')
    <a href="{{ route('especialidades.create') }}" class="btn btn-primary btn-sm">
        <i data-lucide="plus" style="width:16px;height:16px;"></i> Nueva Especialidad
    </a>
@endsection

@section('content')

{{-- Buscador y filtros --}}
<div class="card mb-3">
    <form action="{{ route('especialidades.index') }}" method="GET" class="flex gap-3" style="align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="margin:0;flex:1;min-width:200px;">
            <label class="form-label">Buscar especialidad</label>
            <input type="text" name="buscar" class="form-control"
                placeholder="Nombre de la especialidad..."
                value="{{ request('buscar') }}">
        </div>
        
        <div class="form-group flex align-center" style="margin:0; min-width:140px; margin-bottom: 12px; gap: 8px;">
            <input type="checkbox" name="archivados" id="archivados" value="1" {{ request()->has('archivados') ? 'checked' : '' }} style="width: 16px; height: 16px;">
            <label for="archivados" class="form-label text-danger" style="margin: 0; cursor: pointer; color: #ef4444; font-weight: 600;">Ver Archivadas</label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary"><i data-lucide="search" style="width:14px;height:14px;"></i> Buscar</button>
            @if(request('buscar') || request('archivados'))
                <a href="{{ route('especialidades.index') }}" class="btn btn-secondary">Limpiar</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Especialidades ({{ $especialidades->total() }})</span>
    </div>

    @if($especialidades->isEmpty())
        <p class="text-muted">No se encontraron especialidades.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Médicos Asignados</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($especialidades as $especialidad)
                    <tr>
                        <td style="font-weight: 600;">
                            {{ $especialidad->nombre }}
                            @if($especialidad->trashed())
                                <span style="background: #fee2e2; color: #ef4444; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; margin-left: 4px; font-weight: bold;">ARCHIVADA</span>
                            @endif
                        </td>
                        <td style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $especialidad->descripcion ?: '—' }}
                        </td>
                        <td>
                            <span class="badge badge-info" style="background:#e0e7ff;color:#4f46e5;">{{ $especialidad->especialistas()->count() }} Médicos</span>
                        </td>
                        <td>
                            @if($especialidad->activo)
                                <span class="badge badge-completada">Activa</span>
                            @else
                                <span class="badge badge-cancelada">Inactiva</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-2">
                                @if($especialidad->trashed())
                                    <form action="{{ route('especialidades.restore', $especialidad->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Restaurar esta especialidad?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm" style="background:#10b981; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Restaurar</button>
                                    </form>
                                @else
                                    <a href="{{ route('especialidades.edit', $especialidad->id) }}" class="btn btn-secondary btn-sm">Editar</a>
                                    
                                    <form action="{{ route('especialidades.toggle_status', $especialidad->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($especialidad->activo)
                                            <button type="submit" class="btn btn-sm" style="background:#f59e0b; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Desactivar</button>
                                        @else
                                            <button type="submit" class="btn btn-sm" style="background:#10b981; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Activar</button>
                                        @endif
                                    </form>

                                    <form action="{{ route('especialidades.destroy', $especialidad->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea archivar esta especialidad?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="background:#ef4444; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{ $especialidades->links() }}
        </div>
    @endif
</div>
@endsection
