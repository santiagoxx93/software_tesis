<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// -----------------------------------------------------------------------
// Rutas API para los componentes Vue (requieren sesión web, no Sanctum)
// -----------------------------------------------------------------------
Route::middleware('auth')->group(function () {

    /**
     * Verifica si existe un cruce de horario para un especialista.
     * Usado por FormularioCita.vue para feedback en tiempo real.
     *
     * GET /api/citas/disponibilidad?especialista_id=1&fecha=2026-05-10&hora_inicio=09:00&hora_fin=10:00
     */
    Route::get('/citas/disponibilidad', function (Request $request) {
        $request->validate([
            'especialista_id' => ['required', 'integer', 'exists:especialistas,id'],
            'fecha'           => ['required', 'date'],
            'hora_inicio'     => ['required', 'date_format:H:i'],
            'hora_fin'        => ['required', 'date_format:H:i'],
        ]);

        $cruce = \App\Models\Cita::existeCruceHorario(
            $request->integer('especialista_id'),
            $request->fecha,
            $request->hora_inicio,
            $request->hora_fin,
            $request->integer('excluir_cita_id', 0) ?: null
        );

        return response()->json(['cruce' => $cruce]);
    });

    /**
     * Búsqueda de pacientes en formato JSON.
     * Usado por BuscadorPacientes.vue.
     *
     * GET /api/pacientes?buscar=ana
     */
    Route::get('/pacientes', function (Request $request) {
        $buscar = $request->string('buscar')->trim();

        $pacientes = \App\Models\Paciente::when($buscar->isNotEmpty(), function ($q) use ($buscar) {
            $q->where('nombres', 'like', "%{$buscar}%")
              ->orWhere('apellidos', 'like', "%{$buscar}%")
              ->orWhere('cedula', 'like', "%{$buscar}%");
        })
        ->orderBy('apellidos')
        ->limit(10)
        ->get(['id', 'nombres', 'apellidos', 'cedula', 'telefono']);

        return response()->json($pacientes);
    });

    /**
     * Citas del mes en formato JSON para el CalendarioCitas.vue.
     *
     * GET /api/citas?mes=5&anio=2026
     */
    Route::get('/citas', function (Request $request) {
        $mes  = $request->integer('mes', now()->month);
        $anio = $request->integer('anio', now()->year);

        $citas = \App\Models\Cita::with(['paciente', 'especialista'])
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->when(auth()->user()->esEspecialista(), fn ($q) =>
                $q->where('especialista_id', auth()->user()->especialista?->id)
            )
            ->get()
            ->map(fn ($c) => [
                'id'                => $c->id,
                'fecha'             => $c->fecha->toDateString(),
                'hora_inicio'       => substr($c->hora_inicio, 0, 5),
                'hora_fin'          => substr($c->hora_fin, 0, 5),
                'estado'            => $c->estado,
                'paciente_nombre'   => $c->paciente->nombre_completo,
                'especialista_id'   => $c->especialista_id,
                'especialista_nombre' => $c->especialista->nombre_completo,
                'motivo'            => $c->motivo,
            ]);

        return response()->json($citas);
    });
});
