<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte San Alfonso</title>
    <style>
        @page { size: auto; margin: 0mm; } /* Ocultar encabezados del navegador */
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; font-size: 14px; background: white; }
        
        /* Truco para tener márgenes en todas las páginas con margin: 0 */
        @media print {
            .page-header-space { height: 20mm; }
            .page-footer-space { height: 20mm; }
            .page-header { position: fixed; top: 0; width: 100%; height: 20mm; background: white; z-index: 1000; }
            .page-footer { position: fixed; bottom: 0; width: 100%; height: 20mm; background: white; z-index: 1000; }
        }
        @media screen {
            .page-header-space, .page-footer-space, .page-header, .page-footer { display: none; }
            .content-wrapper { padding-left: 10px; padding-right: 10px; } /* Menos padding en pantalla */
        }
        
        .content-wrapper { padding-left: 20mm; padding-right: 20mm; }
        
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 25px; display: flex; flex-direction: column; align-items: center; }
        .header img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #1a1a1a; }
        .header p { margin: 5px 0 0 0; color: #666; }
        
        .kpi-container { display: flex; gap: 15px; margin-bottom: 30px; }
        .kpi-box { flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 8px; text-align: center; background: #f9f9f9; box-sizing: border-box; }
        .kpi-title { font-size: 12px; text-transform: uppercase; color: #666; margin-bottom: 5px; }
        .kpi-value { font-size: 24px; font-weight: bold; color: #111; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; page-break-inside: auto; }
        table.data-table tr { page-break-inside: avoid; page-break-after: auto; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; }
        
        .section-title { font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 30px; margin-bottom: 15px; page-break-after: avoid; }
        
        .charts-container { display: block; width: 100%; max-width: 100%; margin-bottom: 30px; }
        .chart-wrapper { width: 100%; max-width: 100%; height: 250px; border: 1px solid #ddd; padding: 10px 10px 15px 10px; border-radius: 8px; page-break-inside: avoid; margin-bottom: 25px; box-sizing: border-box; }
        canvas { max-width: 100% !important; max-height: 100% !important; }
        
        .print-btn { display: block; margin: 20px auto; padding: 10px 20px; font-size: 16px; background: #4f6ef7; color: white; border: none; border-radius: 4px; cursor: pointer; }
        
        @media print {
            .no-print { display: none !important; }
            .print-wrapper-table { width: 100%; }
            .print-wrapper-table > thead { display: table-header-group; }
            .print-wrapper-table > tfoot { display: table-footer-group; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="page-header"></div>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="print-btn" style="display:inline-flex; align-items:center;" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> 
            Imprimir PDF / Guardar
        </button>
    </div>

    <table class="print-wrapper-table" style="width: 100%; border: none;">
        <thead><tr><td><div class="page-header-space"></div></td></tr></thead>
        <tbody><tr><td>
            <div class="content-wrapper">
                <div class="header">
                    <img src="/sanalfonzo.png" alt="Logo San Alfonso">
                    <h1>Centro Integral San Alfonso</h1>
                    <p>Reporte Estadístico de Visitas y Pacientes</p>
                    <p style="font-weight: bold;">Periodo: {{ $start->isoFormat('DD/MM/YYYY') }} al {{ $end->isoFormat('DD/MM/YYYY') }}</p>
                </div>

                <div class="kpi-container">
                    <div class="kpi-box">
                        <div class="kpi-title">Visitas Atendidas</div>
                        <div class="kpi-value">{{ $historias->count() }}</div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-title">Citas Programadas</div>
                        <div class="kpi-value">{{ $totalCitas }}</div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-title">Ausencias</div>
                        <div class="kpi-value">{{ $ausencias }}</div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-title">Pacientes Nuevos</div>
                        <div class="kpi-value">{{ $nuevosPacientes }}</div>
                    </div>
                </div>

                <div class="charts-container">
                    <div class="chart-wrapper">
                        <h4 style="margin-top:0; text-align:center;">Pacientes Atendidos por Día</h4>
                        <div style="position:relative; height:200px;">
                            <canvas id="chartDiario"></canvas>
                        </div>
                    </div>

                    <div class="chart-wrapper">
                        <h4 style="margin-top:0; text-align:center;">Atenciones por Especialista</h4>
                        <div style="position:relative; height:200px;">
                            <canvas id="chartEspecialista"></canvas>
                        </div>
                    </div>
                </div>

                <div class="section-title">Resumen Semanal de Visitas</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Semana de inicio (Lunes)</th>
                            <th style="text-align:center;">Visitas Registradas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($atendidosSemanales as $semana => $visitas)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($semana)->locale('es')->isoFormat('DD MMM YYYY') }} (Semana)</td>
                            <td style="text-align:center;">{{ $visitas->count() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" style="text-align:center;">No hay datos</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="section-title">Resumen Mensual de Visitas</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th style="text-align:center;">Visitas Registradas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($atendidosMensuales as $mes => $visitas)
                        <tr>
                            <td style="text-transform: capitalize;">{{ \Carbon\Carbon::parse($mes)->locale('es')->isoFormat('MMMM YYYY') }}</td>
                            <td style="text-align:center;">{{ $visitas->count() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" style="text-align:center;">No hay datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </td></tr></tbody>
        <tfoot><tr><td><div class="page-footer-space"></div></td></tr></tfoot>
    </table>

    <div class="page-footer"></div>

    <script>
        window.addEventListener('load', function() {
            Chart.defaults.animation = false;

            const labelsDiario = {!! json_encode($atendidosDiarios->keys()) !!};
            const dataDiario = {!! json_encode($atendidosDiarios->map->count()->values()) !!};

            new Chart(document.getElementById('chartDiario'), {
                type: 'line',
                data: {
                    labels: labelsDiario,
                    datasets: [{
                        label: 'Visitas',
                        data: dataDiario,
                        borderColor: '#4f6ef7',
                        borderWidth: 2,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { bottom: 10 } },
                    plugins: { legend: { display: false } },
                    scales: { 
                        x: { ticks: { maxRotation: 45, minRotation: 45 } },
                        y: { beginAtZero: true, ticks: { stepSize: 1 } } 
                    }
                }
            });

            const labelsEsp = {!! json_encode($atencionesPorEspecialista->keys()) !!};
            const dataEsp = {!! json_encode($atencionesPorEspecialista->values()) !!};

            new Chart(document.getElementById('chartEspecialista'), {
                type: 'bar',
                data: {
                    labels: labelsEsp,
                    datasets: [{
                        label: 'Visitas',
                        data: dataEsp,
                        backgroundColor: '#38bdf8'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { bottom: 10 } },
                    plugins: { legend: { display: false } },
                    scales: { 
                        x: { ticks: { maxRotation: 0, minRotation: 0 } },
                        y: { beginAtZero: true, ticks: { stepSize: 1 } } 
                    }
                }
            });

            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
