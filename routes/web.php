<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvolucionClinicaController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuarioController;
use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------------------------------
// Autenticación
// -----------------------------------------------------------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Raíz → redirige al login si no está autenticado
Route::get('/', fn () => redirect()->route('login'));

// -----------------------------------------------------------------------
// Rutas protegidas (cualquier usuario autenticado)
// -----------------------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Dashboard (redirige según rol internamente)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // -------------------------------------------------------------------
    // Módulo de Citas (admin + especialista, con restricciones internas)
    // -------------------------------------------------------------------
    Route::prefix('citas')->name('citas.')->group(function () {
        Route::get('/',                [CitaController::class, 'index'])->name('index');
        Route::get('/crear',           [CitaController::class, 'create'])->name('create');
        Route::post('/',               [CitaController::class, 'store'])->name('store');
        Route::get('/{cita}',          [CitaController::class, 'show'])->name('show');
        Route::get('/{cita}/editar',   [CitaController::class, 'edit'])->name('edit');
        Route::put('/{cita}',          [CitaController::class, 'update'])->name('update');
        Route::patch('/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('cambiarEstado');
        Route::delete('/{cita}',       [CitaController::class, 'destroy'])->name('destroy');
    });

    // -------------------------------------------------------------------
    // Módulo de Pacientes
    // -------------------------------------------------------------------
    Route::prefix('pacientes')->name('pacientes.')->group(function () {
        // Listado y perfil básico — cualquier usuario autenticado
        Route::get('/',                     [PacienteController::class, 'index'])->name('index');
        
        // Crear paciente — solo admin (recepción) - DEBE IR ANTES DEL WILDCARD
        Route::get('/crear',                [PacienteController::class, 'create'])->name('create')->middleware('admin');
        Route::post('/',                    [PacienteController::class, 'store'])->name('store')->middleware('admin');

        // Wildcard routes
        Route::get('/{paciente}',           [PacienteController::class, 'show'])->name('show');
        
        // Editar paciente — solo admin
        Route::get('/{paciente}/editar',    [PacienteController::class, 'edit'])->name('edit')->middleware('admin');
        Route::put('/{paciente}',           [PacienteController::class, 'update'])->name('update')->middleware('admin');
    });

    // -------------------------------------------------------------------
    // Respaldos (Solo Admin)
    // -------------------------------------------------------------------
    Route::get('/backup/descargar', [\App\Http\Controllers\BackupController::class, 'download'])
        ->name('backup.download')
        ->middleware('admin');

    // -------------------------------------------------------------------
    // Módulo de Reportes y Estadísticas (Solo Admin)
    // -------------------------------------------------------------------
    Route::get('/reportes', [ReportesController::class, 'index'])
        ->name('reportes.index')
        ->middleware('admin');

    // -------------------------------------------------------------------
    // Módulo de Gestión de Trabajadores (Solo Admin)
    // -------------------------------------------------------------------
    Route::prefix('usuarios')->name('usuarios.')->middleware('admin')->group(function () {
        Route::get('/',              [UsuarioController::class, 'index'])->name('index');
        Route::get('/crear',         [UsuarioController::class, 'create'])->name('create');
        Route::post('/',             [UsuarioController::class, 'store'])->name('store');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'edit'])->name('edit');
        Route::put('/{usuario}',     [UsuarioController::class, 'update'])->name('update');
    });

    // -------------------------------------------------------------------
    // Módulo de Historia Clínica y Evoluciones — solo especialistas
    // -------------------------------------------------------------------
    Route::prefix('historias')->name('historias.')->middleware('especialista')->group(function () {
        Route::get('/',                        [HistoriaClinicaController::class, 'index'])->name('index');
        Route::get('/{historia}',              [HistoriaClinicaController::class, 'show'])->name('show');
        Route::put('/{historia}',              [HistoriaClinicaController::class, 'update'])->name('update');
        Route::post('/{historia}/evoluciones', [EvolucionClinicaController::class, 'store'])->name('evoluciones.store');
        Route::get('/evoluciones/{evolucion}', [EvolucionClinicaController::class, 'show'])->name('evoluciones.show');
    });

    // -------------------------------------------------------------------
    // Endpoints JSON para componentes Vue (prefijo /api con sesión web)
    // -------------------------------------------------------------------
    Route::prefix('api')->group(function () {

        // Verifica cruce de horario en tiempo real
        // GET /api/citas/disponibilidad?especialista_id=1&fecha=2026-05-10&hora_inicio=09:00&hora_fin=10:00
        Route::get('/citas/disponibilidad', function (Request $request) {
            $request->validate([
                'especialista_id' => ['required', 'integer', 'exists:especialistas,id'],
                'fecha'           => ['required', 'date'],
                'hora_inicio'     => ['required', 'date_format:H:i'],
                'hora_fin'        => ['required', 'date_format:H:i'],
            ]);

            return response()->json([
                'cruce' => Cita::existeCruceHorario(
                    $request->integer('especialista_id'),
                    $request->fecha,
                    $request->hora_inicio,
                    $request->hora_fin,
                    $request->integer('excluir_cita_id', 0) ?: null
                ),
            ]);
        })->name('api.citas.disponibilidad');

        // Citas del mes para CalendarioCitas
        // GET /api/citas?mes=5&anio=2026
        Route::get('/citas', function (Request $request) {
            $mes  = $request->integer('mes', now()->month);
            $anio = $request->integer('anio', now()->year);

            $citas = Cita::with(['paciente', 'especialista'])
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', $anio)
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

            return response()->json($citas);
        })->name('api.citas.mes');

        // Búsqueda de pacientes para BuscadorPacientes
        // GET /api/pacientes?buscar=ana
        Route::get('/pacientes', function (Request $request) {
            $buscar = $request->string('buscar')->trim();

            $pacientes = Paciente::when(
                $buscar->isNotEmpty(),
                fn ($q) => $q->where('nombres', 'like', "%{$buscar}%")
                             ->orWhere('apellidos', 'like', "%{$buscar}%")
                             ->orWhere('cedula', 'like', "%{$buscar}%")
            )
            ->orderBy('apellidos')
            ->limit(10)
            ->get(['id', 'nombres', 'apellidos', 'cedula', 'telefono']);

            return response()->json($pacientes);
        })->name('api.pacientes.buscar');
    });
});
