<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista de pacientes con búsqueda rápida.
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

        $pacientes = $query->paginate(20)->withQueryString();

        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Formulario de nuevo paciente.
     * Solo accesible por admin (recepción).
     */
    public function create(): View
    {
        abort_if(! auth()->user()->esAdmin(), 403, 'Solo el personal de recepción puede registrar pacientes.');

        return view('pacientes.create');
    }

    /**
     * Guarda un nuevo paciente.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(! auth()->user()->esAdmin(), 403, 'Solo el personal de recepción puede registrar pacientes.');

        $validated = $request->validate([
            'cedula'              => ['required', 'string', 'max:20', 'unique:pacientes,cedula'],
            'nombres'             => ['required', 'string', 'max:100'],
            'apellidos'           => ['required', 'string', 'max:100'],
            'fecha_nacimiento'    => ['nullable', 'date', 'before:today'],
            'sexo'                => ['nullable', 'in:M,F,Otro'],
            'telefono'            => ['nullable', 'string', 'max:20'],
            'telefono_emergencia' => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'max:150'],
            'direccion'           => ['nullable', 'string'],
            'ocupacion'           => ['nullable', 'string', 'max:100'],
        ]);

        $paciente = Paciente::create($validated);

        // Crear historia clínica vacía automáticamente
        HistoriaClinica::create([
            'paciente_id' => $paciente->id,
            'creado_por'  => auth()->id(),
        ]);

        return redirect()->route('pacientes.show', $paciente)
            ->with('success', "Paciente {$paciente->nombre_completo} registrado exitosamente.");
    }

    /**
     * Perfil del paciente.
     * Admins ven datos de contacto. Especialistas ven la historia clínica completa.
     */
    public function show(Paciente $paciente): View
    {
        $paciente->load(['historiaClinica.evoluciones.especialista', 'citas' => function ($q) {
            $q->with('especialista')->orderBy('fecha', 'desc')->limit(10);
        }]);

        return view('pacientes.show', compact('paciente'));
    }

    /**
     * Formulario de edición (solo admin).
     */
    public function edit(Paciente $paciente): View
    {
        abort_if(! auth()->user()->esAdmin(), 403, 'Solo el personal de recepción puede editar pacientes.');

        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Actualiza los datos del paciente.
     */
    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        abort_if(! auth()->user()->esAdmin(), 403, 'Solo el personal de recepción puede editar pacientes.');

        $validated = $request->validate([
            'cedula'              => ['required', 'string', 'max:20', "unique:pacientes,cedula,{$paciente->id}"],
            'nombres'             => ['required', 'string', 'max:100'],
            'apellidos'           => ['required', 'string', 'max:100'],
            'fecha_nacimiento'    => ['nullable', 'date', 'before:today'],
            'sexo'                => ['nullable', 'in:M,F,Otro'],
            'telefono'            => ['nullable', 'string', 'max:20'],
            'telefono_emergencia' => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'max:150'],
            'direccion'           => ['nullable', 'string'],
            'ocupacion'           => ['nullable', 'string', 'max:100'],
        ]);

        $paciente->update($validated);

        return redirect()->route('pacientes.show', $paciente)
            ->with('success', 'Datos del paciente actualizados.');
    }
}
