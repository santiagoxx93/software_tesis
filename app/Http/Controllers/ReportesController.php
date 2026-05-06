<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportesController extends Controller
{
    /**
     * Muestra el panel principal de reportes y estadísticas.
     */
    public function index(Request $request): View
    {
        // Rango de fechas: por defecto el mes actual
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $end   = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // Consultar citas en el periodo
        $citas = Cita::whereBetween('fecha', [$start->toDateString(), $end->toDateString()])->get();
        
        $totalCitas = $citas->count();
        $citasPorEstado = $citas->groupBy('estado')->map->count();
        
        $completadas = $citasPorEstado->get('completada', 0);
        $ausencias   = $citasPorEstado->get('ausente', 0);
        $canceladas  = $citasPorEstado->get('cancelada', 0);
        
        // Tasa de asistencia (sobre las que ya pasaron/completadas/ausentes)
        $totalEvaluadas = $completadas + $ausencias;
        $tasaAsistencia = $totalEvaluadas > 0 ? round(($completadas / $totalEvaluadas) * 100, 1) : 0;
        
        // Pacientes nuevos
        $nuevosPacientes = Paciente::whereBetween('created_at', [$start, $end])->count();
        
        // Citas por especialista
        $citasPorEspecialista = $citas->load('especialista')
                                      ->groupBy('especialista.nombre_completo')
                                      ->map->count();

        return view('reportes.index', compact(
            'start', 'end', 'totalCitas', 'completadas', 'ausencias', 'canceladas',
            'tasaAsistencia', 'nuevosPacientes', 'citasPorEspecialista'
        ));
    }
}
