<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\HistoriaClinica;

class ReportesController extends Controller
{
    /**
     * Muestra el panel principal de reportes y estadísticas.
     */
    public function index(Request $request): View
    {
        $data = $this->getReportData($request);
        return view('reportes.index', $data);
    }

    /**
     * Exporta los reportes a una vista limpia para imprimir como PDF.
     */
    public function downloadPdf(Request $request): View
    {
        $data = $this->getReportData($request);
        
        return view('reportes.pdf', $data);
    }

    /**
     * Obtiene todos los datos estadísticos unificados.
     */
    private function getReportData(Request $request): array
    {
        // Rango de fechas: por defecto el mes actual
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $end   = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // 1. Estadísticas de Citas Generales
        $citas = Cita::whereBetween('fecha', [$start->toDateString(), $end->toDateString()])->get();
        $totalCitas = $citas->count();
        $citasPorEstado = $citas->groupBy('estado')->map->count();
        $completadas = $citasPorEstado->get('completada', 0);
        $ausencias   = $citasPorEstado->get('ausente', 0);
        $canceladas  = $citasPorEstado->get('cancelada', 0);
        $totalEvaluadas = $completadas + $ausencias;
        $tasaAsistencia = $totalEvaluadas > 0 ? round(($completadas / $totalEvaluadas) * 100, 1) : 0;

        // 2. Ingresos de Pacientes (Pacientes de primera vez registrados)
        $nuevosPacientes = Paciente::whereBetween('created_at', [$start, $end])->count();

        // 3. Pacientes Atendidos (Historias Clínicas / Visitas)
        $historias = HistoriaClinica::with('especialista')
            ->whereBetween('fecha_consulta', [$start->toDateString(), $end->toDateString()])
            ->get();

        // Agrupaciones para pacientes atendidos
        $atendidosDiarios = $historias->groupBy(fn($h) => Carbon::parse($h->fecha_consulta)->format('Y-m-d'))->sortKeys();
        $atendidosSemanales = $historias->groupBy(fn($h) => Carbon::parse($h->fecha_consulta)->startOfWeek()->format('Y-m-d'))->sortKeys();
        $atendidosMensuales = $historias->groupBy(fn($h) => Carbon::parse($h->fecha_consulta)->format('Y-m'))->sortKeys();

        // Atenciones por especialista
        $atencionesPorEspecialista = $historias->groupBy(function($h) {
            return $h->especialista ? $h->especialista->nombre_completo : 'Sin Especialista';
        })->map->count();

        // Atenciones por especialista agrupadas por periodo
        $especialistasDiario = $this->agruparPorEspecialista($atendidosDiarios);
        $especialistasSemanal = $this->agruparPorEspecialista($atendidosSemanales);
        $especialistasMensual = $this->agruparPorEspecialista($atendidosMensuales);

        return compact(
            'start', 'end', 'totalCitas', 'completadas', 'ausencias', 'canceladas',
            'tasaAsistencia', 'nuevosPacientes', 'historias',
            'atendidosDiarios', 'atendidosSemanales', 'atendidosMensuales',
            'atencionesPorEspecialista', 'especialistasDiario', 'especialistasSemanal', 'especialistasMensual'
        );
    }

    /**
     * Agrupa una colección agrupada temporalmente, contando por especialista internamente.
     */
    private function agruparPorEspecialista($coleccionTemporal)
    {
        return $coleccionTemporal->map(function ($historiasDelPeriodo) {
            return $historiasDelPeriodo->groupBy(function($h) {
                return $h->especialista ? $h->especialista->nombre_completo : 'Sin Especialista';
            })->map->count();
        });
    }
}
