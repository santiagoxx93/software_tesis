@extends('layouts.app')

@section('title', 'Gestión de Citas')
@section('page-title', 'Gestión de Citas')
@section('breadcrumb', 'Listado y calendario de citas')

@section('topbar-actions')
    @if(auth()->user()->esAdmin())
        <form action="{{ route('citas.simular_recordatorios') }}" method="POST" style="display:inline-block; margin-right: 10px;" onsubmit="return confirm('¿Deseas enviar los correos de recordatorio para las citas programadas para el día de mañana?');">
            @csrf
            <button type="submit" class="btn btn-sm" style="background:#f59e0b; color:white; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; display:flex; align-items:center; gap:5px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Enviar Recordatorios
            </button>
        </form>
    @endif
    <a href="{{ route('citas.create') }}" class="btn btn-primary btn-sm" id="btn-nueva-cita">
        <i data-lucide="plus" style="width:16px;height:16px;"></i> Nueva Cita
    </a>
@endsection

@section('content')

{{-- Tabs: Calendario | Lista --}}
<div class="tabs-wrapper mb-3">
    <div class="tabs">
        <button class="tab-btn active" id="tab-calendario" onclick="switchTab('calendario')"><i data-lucide="calendar" style="width:16px;height:16px;margin-bottom:-3px;margin-right:4px;"></i> Calendario</button>
        <button class="tab-btn" id="tab-lista" onclick="switchTab('lista')"><i data-lucide="list" style="width:16px;height:16px;margin-bottom:-3px;margin-right:4px;"></i> Lista</button>
    </div>
</div>

{{-- TAB CALENDARIO - Componente Vue --}}
<div id="panel-calendario" class="card">
    <div data-vue-app>
        <calendario-citas
            :citas="{{ json_encode($citasJson) }}"
            :especialistas="{{ json_encode($especialistasJson) }}"
            :es-admin="{{ auth()->user()->esAdmin() ? 'true' : 'false' }}"
        ></calendario-citas>
    </div>
</div>

{{-- TAB LISTA --}}
<div id="panel-lista" style="display:none;">

    {{-- Filtros --}}
    <div class="card mb-3">
        <form action="{{ route('citas.index') }}" method="GET" class="flex gap-3" style="flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin:0;flex:1;min-width:160px;">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:160px;">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    @foreach(['pendiente','completada','cancelada','ausente','reprogramada'] as $e)
                        <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->esAdmin())
            <div class="form-group" style="margin:0;flex:1;min-width:180px;">
                <label class="form-label">Especialista</label>
                <select name="especialista_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($especialistas as $esp)
                        <option value="{{ $esp->id }}" {{ request('especialista_id') == $esp->id ? 'selected' : '' }}>
                            {{ $esp->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group flex align-center" style="margin:0; min-width:140px; margin-bottom: 12px; gap: 8px;">
                <input type="checkbox" name="incluir_pasadas" id="incluir_pasadas" value="1" {{ request()->has('incluir_pasadas') ? 'checked' : '' }} style="width: 16px; height: 16px;">
                <label for="incluir_pasadas" class="form-label" style="margin: 0; cursor: pointer;">Incluir pasadas</label>
            </div>
            <div style="display:flex;gap:.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="filter" style="width:14px;height:14px;"></i> Filtrar</button>
                <a href="{{ route('citas.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Citas ({{ $citas->total() }})</span>
        </div>

        @if($citas->isEmpty())
            <p class="text-muted">No se encontraron citas con los filtros aplicados.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Paciente</th>
                            <th>Especialista</th>
                            <th>Estado</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($citas as $cita)
                        <tr>
                            <td>
                                <strong>{{ $cita->fecha->isoFormat('DD MMM YYYY') }}</strong><br>
                                <span class="text-muted">
                                    {{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}
                                    — {{ \Carbon\Carbon::parse($cita->hora_fin)->format('h:i A') }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('pacientes.show', $cita->paciente) }}">{{ $cita->paciente->nombre_completo }}</a><br>
                                <span class="text-muted" style="font-size:.75rem;">{{ $cita->paciente->cedula }}</span>
                                @if($cita->paciente->telefono)
                                    <br><span class="text-muted" style="font-size:.75rem;"><i data-lucide="phone" style="width:12px;height:12px;margin-bottom:-2px;"></i> {{ $cita->paciente->telefono }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $cita->especialista->nombre_completo }}</div>
                                @if($cita->especialista->telefono)
                                    <span class="text-muted" style="font-size:.75rem;"><i data-lucide="phone" style="width:12px;height:12px;margin-bottom:-2px;"></i> {{ $cita->especialista->telefono }}</span>
                                @endif
                            </td>
                            <td>
                                <div data-vue-app>
                                    <estado-badge estado="{{ $cita->estado }}"></estado-badge>
                                </div>
                            </td>
                            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $cita->motivo ?? '—' }}
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('citas.show', $cita) }}" class="btn btn-primary btn-sm">Ver</a>
                                    <a href="{{ route('citas.edit', $cita) }}" class="btn btn-secondary btn-sm">Editar</a>
                                    @if(auth()->user()->esAdmin())
                                        <form action="{{ route('citas.destroy', $cita) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea ELIMINAR esta cita? (Solo en caso de error).');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm" style="background:#ef4444; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Eliminar</button>
                                        </form>
                                    @endif
                                    @if(in_array($cita->estado, ['pendiente']))
                                        <form action="{{ route('citas.cambiarEstado', $cita) }}" method="POST" title="Marcar como Completada">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="completada">
                                            <button class="btn btn-success btn-sm" style="font-weight:600; padding:4px 8px;">✓ Completada</button>
                                        </form>
                                        <form action="{{ route('citas.cambiarEstado', $cita) }}" method="POST" title="Marcar como Ausente">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="ausente">
                                            <button class="btn btn-danger btn-sm" style="font-weight:600; padding:4px 8px;">✗ Ausente</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2">{{ $citas->links() }}</div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
.tabs-wrapper { border-bottom: 1px solid var(--color-border); }
.tabs { display: flex; gap: 0; }
.tab-btn {
    padding: .6rem 1.25rem;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--color-text-muted);
    font-size: .875rem; font-weight: 500;
    cursor: pointer;
    transition: all .2s;
    font-family: inherit;
    margin-bottom: -1px;
}
.tab-btn:hover  { color: var(--color-text); }
.tab-btn.active { color: var(--color-primary); border-bottom-color: var(--color-primary); }
</style>
@endpush

@push('scripts')
<script>
function switchTab(tab) {
    document.getElementById('panel-calendario').style.display = tab === 'calendario' ? '' : 'none';
    document.getElementById('panel-lista').style.display      = tab === 'lista'       ? '' : 'none';
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    localStorage.setItem('citasTab', tab);
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('page') || urlParams.has('fecha') || urlParams.has('estado') || urlParams.has('especialista_id') || urlParams.has('incluir_pasadas');
    
    let activeTab = localStorage.getItem('citasTab') || 'calendario';
    if (hasFilters) {
        activeTab = 'lista'; // Forzar pestaña de lista si hay filtros o paginación activa
    }
    switchTab(activeTab);
});
</script>
@endpush
