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
        if ($request->has('archivados')) {
            $query = Paciente::onlyTrashed()->orderBy('apellidos');
        } else {
            $query = Paciente::query()->orderBy('apellidos');
        }

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

        if ($request->has('cedula')) {
            $cedulaLimpia = preg_replace('/[^0-9]/', '', $request->cedula);
            $request->merge(['cedula' => 'V-' . $cedulaLimpia]);
        }

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

        return redirect()->route('pacientes.show', $paciente)
            ->with('success', "Paciente {$paciente->nombre_completo} registrado exitosamente.");
    }

    /**
     * Perfil del paciente.
     * Admins ven datos de contacto. Especialistas ven el historial de visitas.
     */
    public function show(Paciente $paciente): View
    {
        $paciente->load(['historiasClinicas.especialista', 'citas' => function ($q) {
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

        if ($request->has('cedula')) {
            $cedulaLimpia = preg_replace('/[^0-9]/', '', $request->cedula);
            $request->merge(['cedula' => 'V-' . $cedulaLimpia]);
        }

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

    /**
     * Elimina lógicamente (SoftDelete) al paciente.
     */
    public function destroy(Paciente $paciente): RedirectResponse
    {
        abort_if(! auth()->user()->esAdmin(), 403, 'Solo el personal de recepción puede eliminar pacientes.');

        try {
            // Cancelar citas pendientes para liberar la agenda
            $paciente->citas()
                ->where('estado', \App\Models\Cita::ESTADO_PENDIENTE)
                ->update([
                    'estado' => \App\Models\Cita::ESTADO_CANCELADA,
                    'motivo_cancelacion' => 'Paciente archivado (baja del sistema).',
                    'cancelado_por' => auth()->id(),
                ]);

            $paciente->delete();
            return redirect()->route('pacientes.index')
                ->with('success', 'Paciente archivado. Todas sus citas pendientes han sido canceladas automáticamente para liberar la agenda.');
        } catch (\Exception $e) {
            return redirect()->route('pacientes.index')
                ->with('error', 'Ocurrió un error inesperado al intentar eliminar el paciente.');
        }
    }

    /**
     * Restaura un paciente que fue archivado (Soft Deleted).
     */
    public function restore($id)
    {
        abort_if(! auth()->user()->esAdmin(), 403, 'Solo el personal de recepción puede restaurar pacientes.');

        try {
            $paciente = Paciente::withTrashed()->findOrFail($id);
            $paciente->restore();

            return redirect()->route('pacientes.index', ['archivados' => 1])
                ->with('success', 'Paciente restaurado exitosamente del archivo.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ocurrió un error inesperado al intentar restaurar el paciente.');
        }
    }
}
