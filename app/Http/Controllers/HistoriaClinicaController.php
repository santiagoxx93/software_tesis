<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoriaClinicaController extends Controller
{
    public function __construct()
    {
        // Solo especialistas pueden acceder a las historias clínicas (Secreto Médico)
        $this->middleware(['auth', 'especialista']);
    }

    /**
     * Muestra la lista de historias clínicas (Mis Pacientes para el especialista).
     */
    public function index(): View
    {
        $especialistaId = auth()->user()->especialista->id ?? null;

        if ($especialistaId) {
            $historias = HistoriaClinica::where('especialista_id', $especialistaId)
                ->with('paciente')
                ->latest('fecha_consulta')
                ->paginate(20);
        } else {
            // Fallback (ej: si un admin tuviera permiso)
            $historias = HistoriaClinica::with('paciente')->latest('fecha_consulta')->paginate(20);
        }

        return view('historias.index', compact('historias'));
    }

    /**
     * Muestra el formulario para crear una nueva historia clínica (visita).
     */
    public function create(Request $request): View
    {
        $paciente = Paciente::findOrFail($request->get('paciente_id'));
        return view('historias.create', compact('paciente'));
    }

    /**
     * Almacena una nueva historia clínica (visita).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paciente_id'              => ['required', 'exists:pacientes,id'],
            'fecha_consulta'           => ['required', 'date'],
            'antecedentes_personales'  => ['nullable', 'string'],
            'antecedentes_familiares'  => ['nullable', 'string'],
            'motivo_consulta'          => ['nullable', 'string', 'max:1000'],
            'grupo_sanguineo'          => ['nullable', 'string', 'max:10'],
            'medicamentos_actuales'    => ['nullable', 'string'],
            'observaciones_iniciales'  => ['nullable', 'string'],
            'evaluacion'               => ['required', 'string'],
            'tratamiento_aplicado'     => ['required', 'string'],
            'respuesta_paciente'       => ['nullable', 'string'],
            'plan_siguiente_sesion'    => ['nullable', 'string'],
        ]);

        $validated['creado_por'] = auth()->id();
        $validated['especialista_id'] = auth()->user()->especialista->id ?? null;

        HistoriaClinica::create($validated);

        return redirect()->route('pacientes.show', $validated['paciente_id'])
            ->with('success', 'Historia Clínica registrada correctamente.');
    }

    /**
     * Muestra la historia clínica completa de un paciente.
     */
    public function show(HistoriaClinica $historia): View
    {
        $historia->load([
            'paciente',
            'especialista',
            'creadoPor',
        ]);

        return view('historias.show', compact('historia'));
    }

    /**
     * Actualiza la historia clínica.
     */
    public function update(Request $request, HistoriaClinica $historia): RedirectResponse
    {
        if ($historia->estaBloqueado()) {
            return back()->with('error', 'Esta historia clínica está bloqueada y no puede ser editada.');
        }

        $validated = $request->validate([
            'fecha_consulta'           => ['required', 'date'],
            'antecedentes_personales'  => ['nullable', 'string'],
            'antecedentes_familiares'  => ['nullable', 'string'],
            'motivo_consulta'          => ['nullable', 'string', 'max:1000'],
            'grupo_sanguineo'          => ['nullable', 'string', 'max:10'],
            'medicamentos_actuales'    => ['nullable', 'string'],
            'observaciones_iniciales'  => ['nullable', 'string'],
            'evaluacion'               => ['required', 'string'],
            'tratamiento_aplicado'     => ['required', 'string'],
            'respuesta_paciente'       => ['nullable', 'string'],
            'plan_siguiente_sesion'    => ['nullable', 'string'],
        ]);

        $historia->update($validated);

        return back()->with('success', 'Historia clínica actualizada.');
    }
}
