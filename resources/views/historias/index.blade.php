@extends('layouts.app')

@section('title', 'Mis Pacientes')
@section('page-title', 'Mis Pacientes (Historias Clínicas)')
@section('breadcrumb', 'Listado de pacientes que he atendido')

@section('content')

<div class="card mb-3">
    <div class="card-header" style="margin-bottom:0; padding-bottom:0; border-bottom:none;">
        <span class="card-title">Pacientes Atendidos ({{ $historias->total() }})</span>
    </div>
    <p class="text-muted" style="padding: 0 1.5rem 1.5rem;">
        Aquí encontrarás únicamente las historias clínicas de los pacientes en los que has registrado alguna evolución médica.
    </p>

    @if($historias->isEmpty())
        <div style="padding: 0 1.5rem 1.5rem;">
            <p class="text-muted">Aún no has registrado evoluciones en ninguna historia clínica.</p>
        </div>
    @else
        <div class="table-wrap" style="padding: 0 1.5rem 1.5rem;">
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Última Actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historias as $historia)
                    <tr>
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
                            <span title="{{ $historia->updated_at }}">
                                {{ $historia->updated_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('historias.show', $historia) }}" class="btn btn-primary btn-sm">Ver Historia Clínica</a>
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
