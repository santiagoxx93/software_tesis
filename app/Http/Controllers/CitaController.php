<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialista;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista de citas con filtros de fecha y estado.
     */
    public function index(Request $request): View
    {
        $query = Cita::with(['paciente', 'especialista'])
            ->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc');

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('especialista_id')) {
            $query->where('especialista_id', $request->especialista_id);
        }

        // El especialista solo ve sus propias citas
        if (auth()->user()->esEspecialista()) {
            $especialista = auth()->user()->especialista;
            $query->where('especialista_id', $especialista?->id);
        }

        $citas        = $query->paginate(15)->withQueryString();
        $especialistas = Especialista::where('activo', true)->get();

        // Datos para el CalendarioCitas.vue (mes actual)
        $citasJson = Cita::with(['paciente', 'especialista'])
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->when(auth()->user()->esEspecialista(), fn ($q) =>
                $q->where('especialista_id', auth()->user()->especialista?->id)
            )
            ->get()
            ->map(fn ($c) => [
                'id'                  => $c->id,
                'fecha'               => $c->fecha->toDateString(),
                'hora_inicio'         => substr($c->hora_inicio, 0, 5),
                'hora_fin'            => substr($c->hora_fin, 0, 5),
                'estado'              => $c->estado,
                'paciente_nombre'     => $c->paciente->nombre_completo,
                'especialista_id'     => $c->especialista_id,
                'especialista_nombre' => $c->especialista->nombre_completo,
                'motivo'              => $c->motivo,
            ]);

        $especialistasJson = $especialistas->map(fn ($e) => [
            'id'       => $e->id,
            'nombres'  => $e->nombres,
            'apellidos'=> $e->apellidos,
        ]);

        return view('citas.index', compact('citas', 'especialistas', 'citasJson', 'especialistasJson'));
    }

    /**
     * Formulario de nueva cita.
     */
    public function create(): View
    {
        $pacientes    = Paciente::orderBy('apellidos')->get();
        $especialistas = Especialista::where('activo', true)->orderBy('apellidos')->get();

        return view('citas.create', compact('pacientes', 'especialistas'));
    }

    /**
     * Guarda una nueva cita con validación de cruce de horarios.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paciente_id'    => ['required', 'exists:pacientes,id'],
            'especialista_id'=> ['required', 'exists:especialistas,id'],
            'fecha'          => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio'    => ['required', 'date_format:H:i'],
            'hora_fin'       => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo'         => ['nullable', 'string', 'max:500'],
            'notas_recepcion'=> ['nullable', 'string', 'max:1000'],
        ]);

        // Validación de duplicidad de horario
        if (Cita::existeCruceHorario(
            $validated['especialista_id'],
            $validated['fecha'],
            $validated['hora_inicio'],
            $validated['hora_fin']
        )) {
            return back()
                ->withInput()
                ->withErrors(['hora_inicio' => 'El especialista ya tiene una cita en ese horario. Por favor selecciona otro bloque de tiempo.']);
        }

        Cita::create([
            ...$validated,
            'estado'        => Cita::ESTADO_PENDIENTE,
            'registrado_por'=> auth()->id(),
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita registrada exitosamente.');
    }

    /**
     * Formulario de edición de cita.
     */
    public function edit(Cita $cita): View
    {
        $pacientes    = Paciente::orderBy('apellidos')->get();
        $especialistas = Especialista::where('activo', true)->orderBy('apellidos')->get();

        return view('citas.edit', compact('cita', 'pacientes', 'especialistas'));
    }

    /**
     * Actualiza los datos de la cita.
     */
    public function update(Request $request, Cita $cita): RedirectResponse
    {
        $validated = $request->validate([
            'paciente_id'    => ['required', 'exists:pacientes,id'],
            'especialista_id'=> ['required', 'exists:especialistas,id'],
            'fecha'          => ['required', 'date'],
            'hora_inicio'    => ['required', 'date_format:H:i'],
            'hora_fin'       => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo'         => ['nullable', 'string', 'max:500'],
            'notas_recepcion'=> ['nullable', 'string', 'max:1000'],
        ]);

        // Validación de cruce excluyendo la propia cita
        if (Cita::existeCruceHorario(
            $validated['especialista_id'],
            $validated['fecha'],
            $validated['hora_inicio'],
            $validated['hora_fin'],
            $cita->id
        )) {
            return back()
                ->withInput()
                ->withErrors(['hora_inicio' => 'El especialista ya tiene una cita en ese horario.']);
        }

        $cita->update($validated);

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente.');
    }

    /**
     * Cambia el estado de una cita (confirmar, cancelar, marcar ausente, etc.).
     */
    public function cambiarEstado(Request $request, Cita $cita): RedirectResponse
    {
        $request->validate([
            'estado'            => ['required', 'in:pendiente,confirmada,completada,cancelada,ausente,reprogramada'],
            'motivo_cancelacion'=> ['required_if:estado,cancelada', 'nullable', 'string', 'max:500'],
        ]);

        $datosActualizar = ['estado' => $request->estado];

        if ($request->estado === Cita::ESTADO_CANCELADA) {
            $datosActualizar['cancelado_por']      = auth()->id();
            $datosActualizar['motivo_cancelacion'] = $request->motivo_cancelacion;
        }

        $cita->update($datosActualizar);

        return back()->with('success', 'Estado de la cita actualizado.');
    }

    /**
     * Elimina (soft delete) una cita.
     */
    public function destroy(Cita $cita): RedirectResponse
    {
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada.');
    }
}
