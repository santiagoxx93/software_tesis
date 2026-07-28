@extends('layouts.app')

@section('title', 'Mis Visitas (Historias Clínicas)')
@section('page-title', 'Mis Visitas')
@section('breadcrumb', 'Listado de visitas que he atendido')

@section('content')

<div class="card mb-3">
    <div class="card-header" style="border-bottom:none;">
        <span class="card-title">Visitas Registradas ({{ $historias->total() }})</span>
    </div>
    <p class="text-muted" style="padding: 0.5rem 1.5rem 1.5rem; margin-top: 0;">
        Aquí encontrarás el registro de todas las visitas/historias clínicas que has atendido.
    </p>

    @if($historias->isEmpty())
        <div style="padding: 0 1.5rem 1.5rem;">
            <p class="text-muted">Aún no has registrado ninguna visita.</p>
        </div>
    @else
        <div class="table-wrap" style="padding: 0 1.5rem 1.5rem;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha de Consulta</th>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historias as $historia)
                    <tr>
                        <td>{{ $historia->fecha_consulta?->isoFormat('DD/MM/YYYY') ?? '—' }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:34px;height:34px;background:linear-gradient(135deg,#7c5ef7,#4f6ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($historia->paciente->nombres,0,1).substr($historia->paciente->apellidos,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500;">{{ $historia->paciente->nombre_completo }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">{{ $historia->paciente->ocupacion ?? 'Sin ocupación' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $historia->paciente->cedula }}</td>
                        <td>
                            <a href="{{ route('historias.show', $historia) }}" class="btn btn-primary btn-sm">Ver Visita</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2" style="padding: 0 1.5rem 1.5rem;">
            {{ $historias->links() }}
        </div>
    @endif
</div>
@endsection
