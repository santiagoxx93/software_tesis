@extends('layouts.app')

@section('title', 'Historia Clínica — ' . $historia->paciente->nombre_completo)
@section('page-title', 'Historia Clínica')
@section('breadcrumb', 'Pacientes / ' . $historia->paciente->nombre_completo . ' / HC')

@section('topbar-actions')
    <a href="{{ route('pacientes.show', $historia->paciente) }}" class="btn btn-secondary btn-sm">← Perfil</a>
@endsection

@section('content')

<div class="grid-2" style="align-items:start;gap:1.5rem;">

    {{-- ===== PANEL IZQUIERDO: DATOS DE LA HC ===== --}}
    <div>
        {{-- Datos del paciente --}}
        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title">🩺 Historia Clínica — {{ $historia->paciente->nombre_completo }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#4f6ef7,#7c5ef7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr($historia->paciente->nombres,0,1).substr($historia->paciente->apellidos,0,1)) }}
                </div>
                <div>
                    <div style="font-weight:600;">{{ $historia->paciente->nombre_completo }}</div>
                    <div class="text-muted" style="font-size:.78rem;">
                        {{ $historia->paciente->cedula }} ·
                        {{ $historia->paciente->fecha_nacimiento?->isoFormat('DD/MM/YYYY') ?? 'S/F' }}
                        {{ $historia->paciente->edad ? "({$historia->paciente->edad} años)" : '' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Antecedentes —editable por especialistas --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📋 Antecedentes</span>
            </div>
            <form action="{{ route('historias.update', $historia) }}" method="POST" id="form-historia">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Motivo de consulta inicial</label>
                    <textarea name="motivo_consulta" class="form-control" rows="2">{{ old('motivo_consulta', $historia->motivo_consulta) }}</textarea>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Grupo sanguíneo</label>
                        <input type="text" name="grupo_sanguineo" class="form-control"
                            value="{{ old('grupo_sanguineo', $historia->grupo_sanguineo) }}"
                            placeholder="A+, O-, AB+ ...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Medicamentos actuales</label>
                        <input type="text" name="medicamentos_actuales" class="form-control"
                            value="{{ old('medicamentos_actuales', $historia->medicamentos_actuales) }}"
                            placeholder="Ninguno / lista de medicamentos">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Antecedentes personales</label>
                    <textarea name="antecedentes_personales" class="form-control" rows="3"
                        placeholder="Enfermedades previas, cirugías, alergias...">{{ old('antecedentes_personales', $historia->antecedentes_personales) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Antecedentes familiares</label>
                    <textarea name="antecedentes_familiares" class="form-control" rows="2"
                        placeholder="Historial familiar relevante...">{{ old('antecedentes_familiares', $historia->antecedentes_familiares) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Observaciones iniciales</label>
                    <textarea name="observaciones_iniciales" class="form-control" rows="2">{{ old('observaciones_iniciales', $historia->observaciones_iniciales) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" id="btn-guardar-hc">Guardar antecedentes</button>
            </form>
        </div>
    </div>

    {{-- ===== PANEL DERECHO: EVOLUCIONES ===== --}}
    <div>
        {{-- Formulario nueva evolución --}}
        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title">➕ Nueva evolución</span>
            </div>
            <form action="{{ route('historias.evoluciones.store', $historia) }}" method="POST" id="form-evolucion">
                @csrf

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Fecha de consulta *</label>
                        <input type="date" name="fecha_consulta" class="form-control {{ $errors->has('fecha_consulta') ? 'is-invalid' : '' }}"
                            value="{{ old('fecha_consulta', date('Y-m-d')) }}" required>
                        @error('fecha_consulta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cita vinculada (opcional)</label>
                        <select name="cita_id" class="form-select">
                            <option value="">— Sin vincular —</option>
                            @foreach($historia->paciente->citas->where('estado','completada') as $c)
                                <option value="{{ $c->id }}">{{ $c->fecha->isoFormat('DD/MM/YYYY') }} — {{ \Carbon\Carbon::parse($c->hora_inicio)->format('h:i A') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Evaluación clínica *</label>
                    <textarea name="evaluacion" class="form-control {{ $errors->has('evaluacion') ? 'is-invalid' : '' }}" rows="3"
                        placeholder="Evaluación del estado del paciente en esta sesión..." required>{{ old('evaluacion') }}</textarea>
                    @error('evaluacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tratamiento aplicado *</label>
                    <textarea name="tratamiento_aplicado" class="form-control {{ $errors->has('tratamiento_aplicado') ? 'is-invalid' : '' }}" rows="3"
                        placeholder="Técnicas y zonas de reflexología tratadas..." required>{{ old('tratamiento_aplicado') }}</textarea>
                    @error('tratamiento_aplicado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Respuesta del paciente</label>
                    <textarea name="respuesta_paciente" class="form-control" rows="2"
                        placeholder="Cómo respondió el paciente al tratamiento...">{{ old('respuesta_paciente') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Plan para la próxima sesión</label>
                    <textarea name="plan_siguiente_sesion" class="form-control" rows="2"
                        placeholder="Indicaciones y objetivos para la próxima visita...">{{ old('plan_siguiente_sesion') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" id="btn-guardar-evolucion">
                    Registrar evolución
                </button>
            </form>
        </div>

        {{-- Línea de tiempo de evoluciones --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">🕐 Historial de evoluciones ({{ $historia->evoluciones->count() }})</span>
            </div>
            @if($historia->evoluciones->isEmpty())
                <p class="text-muted">No hay evoluciones registradas aún.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    @foreach($historia->evoluciones as $evolucion)
                    <div style="background:var(--color-surface-2);border:1px solid var(--color-border);border-radius:var(--radius-sm);padding:1rem;position:relative;">
                        <div class="flex-between mb-1">
                            <strong style="font-size:.9rem;">{{ $evolucion->fecha_consulta->isoFormat('DD [de] MMMM, YYYY') }}</strong>
                            <span class="text-muted" style="font-size:.75rem;">{{ $evolucion->especialista->nombre_completo }}</span>
                        </div>
                        <p class="text-muted" style="font-size:.8rem;margin-bottom:.5rem;"><strong style="color:var(--color-text);">Evaluación:</strong> {{ $evolucion->evaluacion }}</p>
                        <p class="text-muted" style="font-size:.8rem;margin-bottom:.5rem;"><strong style="color:var(--color-text);">Tratamiento:</strong> {{ $evolucion->tratamiento_aplicado }}</p>
                        @if($evolucion->respuesta_paciente)
                        <p class="text-muted" style="font-size:.8rem;margin-bottom:.5rem;"><strong style="color:var(--color-text);">Respuesta:</strong> {{ $evolucion->respuesta_paciente }}</p>
                        @endif
                        @if($evolucion->plan_siguiente_sesion)
                        <p class="text-muted" style="font-size:.8rem;margin-bottom:0;"><strong style="color:var(--color-text);">Próxima sesión:</strong> {{ $evolucion->plan_siguiente_sesion }}</p>
                        @endif
                        @if($evolucion->estaBloqueado())
                        <div style="position:absolute;top:.75rem;right:.75rem;">
                            <span style="font-size:.68rem;color:var(--color-text-muted);">🔒 Bloqueado</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
