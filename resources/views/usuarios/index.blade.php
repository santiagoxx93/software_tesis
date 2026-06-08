@extends('layouts.app')

@section('title', 'Trabajadores')
@section('page-title', 'Gestión de Trabajadores')
@section('breadcrumb', 'Listado de usuarios del sistema')

@section('topbar-actions')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm" id="btn-nuevo-usuario">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Trabajador
    </a>
@endsection

@section('content')

{{-- Buscador y filtros --}}
<div class="card mb-3">
    <form action="{{ route('usuarios.index') }}" method="GET" class="flex gap-3" style="align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="margin:0;flex:1;min-width:200px;">
            <label class="form-label">Buscar trabajador</label>
            <input type="text" name="buscar" class="form-control"
                placeholder="Nombre o correo..."
                value="{{ request('buscar') }}"
                id="input-buscar-usuario">
        </div>
        <div class="form-group" style="margin:0;min-width:160px;">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" id="select-filtro-rol">
                <option value="">— Todos —</option>
                <option value="admin" {{ request('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="especialista" {{ request('rol') === 'especialista' ? 'selected' : '' }}>Especialista</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
            @if(request('buscar') || request('rol'))
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Trabajadores ({{ $usuarios->total() }})</span>
    </div>

    @if($usuarios->isEmpty())
        <p class="text-muted">No se encontraron trabajadores.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Trabajador</th>
                        <th>Correo / Teléfono</th>
                        <th>Rol</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:34px;height:34px;background:linear-gradient(135deg,{{ $usuario->hasRole('admin') ? '#f59e0b,#ef4444' : '#4f6ef7,#7c5ef7' }});border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500;">{{ $usuario->name }}</div>
                                    @if($usuario->especialista)
                                        <div class="text-muted" style="font-size:.72rem;">{{ $usuario->especialista->cedula }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $usuario->email }}</div>
                            @if($usuario->especialista && $usuario->especialista->telefono)
                                <div class="text-muted" style="font-size:.75rem;">📞 {{ $usuario->especialista->telefono }}</div>
                            @endif
                        </td>
                        <td>
                            @if($usuario->hasRole('admin'))
                                <span class="badge badge-confirmada">Administrador</span>
                            @elseif($usuario->hasRole('especialista'))
                                <span class="badge badge-completada">Especialista</span>
                            @else
                                <span class="badge badge-ausente">Sin rol</span>
                            @endif
                        </td>
                        <td>{{ $usuario->especialista->especialidad ?? '—' }}</td>
                        <td>
                            @if($usuario->activo)
                                <span class="badge badge-completada">Activo</span>
                            @else
                                <span class="badge badge-cancelada">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-secondary btn-sm">Editar</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>
@endsection
