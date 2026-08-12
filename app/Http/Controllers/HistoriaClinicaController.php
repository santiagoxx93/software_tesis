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
     * Muestra la lista de pacientes para el especialista con buscador.
     */
    public function index(Request $request): View
    {
        $query = Paciente::query()->orderBy('apellidos');

        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->where(function ($q) use ($termino) {
                $q->where('nombres', 'like', "%{$termino}%")
                  ->orWhere('apellidos', 'like', "%{$termino}%")
                  ->orWhere('cedula', 'like', "%{$termino}%");
            });
        }

        $pacientes = $query->paginate(15);
        return view('historias.index', compact('pacientes'));
    }

    /**
     * Muestra la línea de tiempo de evoluciones de un paciente.
     */
    public function paciente(Paciente $paciente): View
    {
        $especialistaId = auth()->user()->especialista->id ?? null;

        $query = $paciente->historiasClinicas()->with('especialista');
        
        if ($especialistaId) {
            $query->where('especialista_id', $especialistaId);
        }

        $historias = $query->latest('fecha_consulta')->get();

        return view('historias.paciente', compact('paciente', 'historias'));
    }

    /**
     * Muestra el formulario para crear una nueva historia clínica (visita).
     */
    public function create(Request $request): View
    {
        $paciente = Paciente::findOrFail($request->get('paciente_id'));
        $ultimaHistoria = $paciente->historiasClinicas()->latest('fecha_consulta')->first();
        
        return view('historias.create', compact('paciente', 'ultimaHistoria'));
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
