<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $archivados = $request->has('archivados');

        $query = Especialidad::query();

        if ($archivados) {
            $query->onlyTrashed();
        }

        if ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
        }

        $especialidades = $query->orderBy('nombre')->paginate(10)->withQueryString();

        return view('especialidades.index', compact('especialidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('especialidades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:especialidades,nombre',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        Especialidad::create($validated);

        return redirect()->route('especialidades.index')->with('success', 'Especialidad creada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $especialidad = Especialidad::withTrashed()->findOrFail($id);
        return view('especialidades.edit', compact('especialidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $especialidad = Especialidad::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:especialidades,nombre,' . $especialidad->id,
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        $especialidad->update($validated);

        return redirect()->route('especialidades.index')->with('success', 'Especialidad actualizada correctamente.');
    }

    /**
     * Activa o desactiva de forma rápida.
     */
    public function toggleStatus(Especialidad $especialidad): RedirectResponse
    {
        $especialidad->update(['activo' => !$especialidad->activo]);
        
        $mensaje = $especialidad->activo ? 'Especialidad activada.' : 'Especialidad desactivada.';
        return redirect()->route('especialidades.index')->with('success', $mensaje);
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Especialidad $especialidad): RedirectResponse
    {
        if ($especialidad->especialistas()->count() > 0) {
            return redirect()->route('especialidades.index')->with('error', 'No se puede eliminar esta especialidad porque hay médicos asignados a ella. Desactívela en lugar de eliminarla.');
        }

        $especialidad->delete();
        return redirect()->route('especialidades.index')->with('success', 'Especialidad archivada correctamente.');
    }

    /**
     * Restaura una especialidad archivada.
     */
    public function restore($id): RedirectResponse
    {
        $especialidad = Especialidad::withTrashed()->findOrFail($id);
        $especialidad->restore();

        return redirect()->route('especialidades.index', ['archivados' => 1])->with('success', 'Especialidad restaurada exitosamente.');
    }
}
