<?php

namespace App\Http\Controllers;

use App\Models\EvolucionClinica;
use App\Models\HistoriaClinica;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvolucionClinicaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'especialista']);
    }

    /**
     * Registra una nueva evolución clínica en la historia del paciente.
     */
    public function store(Request $request, HistoriaClinica $historia): RedirectResponse
    {
        $validated = $request->validate([
            'cita_id'               => ['nullable', 'exists:citas,id'],
            'fecha_consulta'        => ['required', 'date'],
            'evaluacion'            => ['required', 'string'],
            'tratamiento_aplicado'  => ['required', 'string'],
            'respuesta_paciente'    => ['nullable', 'string'],
            'plan_siguiente_sesion' => ['nullable', 'string'],
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $especialista = $user->especialista;

        if (! $especialista) {
            return back()->withErrors(['general' => 'No se encontró el perfil de especialista asociado a tu cuenta.']);
        }

        EvolucionClinica::create([
            ...$validated,
            'historia_clinica_id' => $historia->id,
            'especialista_id'     => $especialista->id,
        ]);

        return redirect()->route('historias.show', $historia)
            ->with('success', 'Evolución clínica registrada exitosamente.');
    }

    /**
     * Muestra el detalle de una evolución clínica.
     */
    public function show(EvolucionClinica $evolucion): \Illuminate\View\View
    {
        $evolucion->load(['historiaClinica.paciente', 'especialista', 'cita']);

        return view('historias.evolucion', compact('evolucion'));
    }
}
