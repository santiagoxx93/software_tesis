<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
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
     * Muestra la historia clínica completa de un paciente.
     */
    public function show(HistoriaClinica $historia): View
    {
        $historia->load([
            'paciente',
            'evoluciones.especialista',
            'creadoPor',
        ]);

        return view('historias.show', compact('historia'));
    }

    /**
     * Actualiza los antecedentes de la historia clínica.
     */
    public function update(Request $request, HistoriaClinica $historia): RedirectResponse
    {
        $validated = $request->validate([
            'antecedentes_personales'  => ['nullable', 'string'],
            'antecedentes_familiares'  => ['nullable', 'string'],
            'motivo_consulta'          => ['nullable', 'string', 'max:1000'],
            'grupo_sanguineo'          => ['nullable', 'string', 'max:10'],
            'medicamentos_actuales'    => ['nullable', 'string'],
            'observaciones_iniciales'  => ['nullable', 'string'],
        ]);

        $historia->update($validated);

        return back()->with('success', 'Historia clínica actualizada.');
    }
}
