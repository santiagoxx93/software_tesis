@extends('layouts.app')

@section('title', 'Línea de Tiempo — ' . $paciente->nombre_completo)
@section('page-title', 'Historial y Evoluciones')
@section('breadcrumb', 'Historias Clínicas / ' . $paciente->nombre_completo)

@section('topbar-actions')
    <a href="{{ route('historias.index') }}" class="btn btn-secondary btn-sm">← Volver al buscador</a>
@endsection

@section('content')

<div class="card mb-4" style="background: linear-gradient(135deg, #f8fafc, #eff6ff); border: 1px solid #bfdbfe;">
    <div style="display:flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:56px;height:56px;background:linear-gradient(135deg,#4f6ef7,#7c5ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;color:#fff;flex-shrink:0;box-shadow: 0 4px 6px -1px rgba(79, 110, 247, 0.2);">
                {{ strtoupper(substr($paciente->nombres,0,1).substr($paciente->apellidos,0,1)) }}
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--color-text);">{{ $paciente->nombre_completo }}</h3>
                <div class="text-muted" style="font-size:.85rem; margin-top: 4px;">
                    Cédula: <strong>{{ $paciente->cedula }}</strong> &nbsp;|&nbsp; 
                    Tel: <strong>{{ $paciente->telefono ?? 'N/A' }}</strong> &nbsp;|&nbsp;
                    Edad: <strong>{{ $paciente->edad ? $paciente->edad . ' años' : 'N/A' }}</strong>
                </div>
            </div>
        </div>
        <div>
            <form action="{{ route('historias.create') }}" method="GET">
                <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.25rem; font-size: 0.95rem;">
                    <i data-lucide="plus-circle" style="width:18px;height:18px;margin-right:6px;"></i> Registrar Nueva Evolución
                </button>
            </form>
        </div>
    </div>
</div>

<h4 style="margin-bottom: 1.5rem; font-size: 1.1rem; display:flex; align-items:center; gap:0.5rem;">
    <i data-lucide="activity" style="color:var(--color-primary); width:20px; height:20px;"></i> Línea de tiempo de visitas
</h4>

@if($historias->isEmpty())
    <div class="card" style="text-align: center; padding: 3rem 1rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--color-bg); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i data-lucide="folder-open" style="width: 32px; height: 32px; color: var(--color-text-muted);"></i>
        </div>
        <h5 style="margin: 0 0 0.5rem; font-weight: 600;">Sin evoluciones registradas</h5>
        <p class="text-muted" style="margin: 0; max-width: 400px; margin: 0 auto;">Este paciente aún no tiene ninguna visita médica registrada en su historial clínico.</p>
    </div>
@else
    <div style="position: relative; padding-left: 1rem;">
        <!-- Línea vertical del timeline -->
        <div style="position: absolute; left: 24px; top: 10px; bottom: 0; width: 2px; background: #e2e8f0; z-index: 1;"></div>
        
        @foreach($historias as $historia)
            <div style="position: relative; padding-left: 3rem; margin-bottom: 1.5rem; z-index: 2;">
                <!-- Punto del timeline -->
                <div style="position: absolute; left: 1px; top: 12px; width: 14px; height: 14px; border-radius: 50%; background: #fff; border: 3px solid var(--color-primary); z-index: 3;"></div>
                
                <div class="card" style="margin-bottom: 0; transition: transform 0.2s, box-shadow 0.2s; border: 1px solid var(--color-border);">
                    <div class="flex-between" style="margin-bottom: 1rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-weight: 600; font-size: 1.05rem; display: block; margin-bottom: 2px;">
                                Visita del {{ $historia->fecha_consulta->isoFormat('D \d\e MMMM YYYY') }}
                            </span>
                            <span class="text-muted" style="font-size: 0.85rem;">
                                Atendido por: <strong>{{ $historia->especialista->nombre_completo ?? 'N/A' }}</strong>
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('historias.show', $historia) }}" class="btn btn-secondary btn-sm"><i data-lucide="edit" style="width:14px;height:14px;margin-bottom:-2px;margin-right:4px;"></i> Ver / Editar</a>
                        </div>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <h5 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); margin-bottom: 0.5rem;">Evaluación Clínica</h5>
                            <p style="font-size: 0.95rem; margin: 0; line-height: 1.5;">
                                {{ Str::limit($historia->evaluacion ?? 'No registrada.', 150) }}
                            </p>
                        </div>
                        <div>
                            <h5 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); margin-bottom: 0.5rem;">Tratamiento Aplicado</h5>
                            <p style="font-size: 0.95rem; margin: 0; line-height: 1.5;">
                                {{ Str::limit($historia->tratamiento_aplicado ?? 'No registrado.', 150) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
