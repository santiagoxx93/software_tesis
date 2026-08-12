<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cita;
use App\Mail\RecordatorioCitaMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCitaReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'citas:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos electrónicos de recordatorio para las citas del día siguiente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando envío de recordatorios...');

        $manana = Carbon::tomorrow()->toDateString();
        
        // Buscar citas confirmadas o pendientes para el día de mañana
        $citas = Cita::with(['paciente', 'especialista'])
            ->whereDate('fecha', $manana)
            ->where('estado', Cita::ESTADO_PENDIENTE)
            ->get();

        $enviados = 0;

        foreach ($citas as $cita) {
            if ($cita->paciente && $cita->paciente->email) {
                try {
                    Mail::to($cita->paciente->email)->send(new RecordatorioCitaMail($cita));
                    $enviados++;
                    $this->line("Recordatorio enviado a: {$cita->paciente->email}");
                } catch (\Exception $e) {
                    Log::error("Error al enviar recordatorio a {$cita->paciente->email}: " . $e->getMessage());
                    $this->error("Error al enviar a {$cita->paciente->email}");
                }
            }
        }

        $this->info("Proceso finalizado. Recordatorios enviados: {$enviados}");
        return Command::SUCCESS;
    }
}
