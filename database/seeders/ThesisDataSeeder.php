<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Especialista;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThesisDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();
        
        // Generar 25 nuevos pacientes registrados este mes para que suba la métrica
        $nombresFaker = ['Carlos', 'Luis', 'María', 'Ana', 'Pedro', 'Jesús', 'José', 'Rosa', 'Carmen', 'Jorge'];
        $apellidosFaker = ['Pérez', 'Gómez', 'Díaz', 'Mendoza', 'Rodríguez', 'López', 'González', 'Martínez', 'Sánchez'];
        
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        for ($i = 1; $i <= 25; $i++) {
            Paciente::create([
                'cedula'              => 'V-' . rand(10000000, 30000000),
                'nombres'             => $nombresFaker[array_rand($nombresFaker)] . ' ' . $nombresFaker[array_rand($nombresFaker)],
                'apellidos'           => $apellidosFaker[array_rand($apellidosFaker)] . ' ' . $apellidosFaker[array_rand($apellidosFaker)],
                'fecha_nacimiento'    => Carbon::now()->subYears(rand(18, 65))->format('Y-m-d'),
                'sexo'                => rand(0, 1) ? 'M' : 'F',
                'telefono'            => '0414-' . rand(1000000, 9999999),
                'created_at'          => Carbon::createFromTimestamp(rand($inicioMes->timestamp, $finMes->timestamp))
            ]);
        }

        $pacientes = Paciente::all();
        $especialistas = Especialista::all();

        // Vamos a generar citas. Queremos unas 70 citas para el mes actual, la mayoría "completadas"
        $citasACrear = 85; 

        for ($i = 0; $i < $citasACrear; $i++) {
            $paciente = $pacientes->random();
            $especialista = $especialistas->random();
            
            // Fecha aleatoria dentro de este mes
            $fechaAleatoria = Carbon::createFromTimestamp(rand($inicioMes->timestamp, $finMes->timestamp));
            
            if ($fechaAleatoria->isWeekend()) {
                $fechaAleatoria->subDays(2);
            }

            // Forzar que el 80% sean completadas (o confirmadas si son en el futuro)
            $estadoAleatorio = Cita::ESTADO_COMPLETADA;
            if (rand(1, 10) > 8) {
                $estadoAleatorio = [Cita::ESTADO_AUSENTE, Cita::ESTADO_CANCELADA, Cita::ESTADO_PENDIENTE][rand(0, 2)];
            }
            
            if ($fechaAleatoria->isFuture() && in_array($estadoAleatorio, [Cita::ESTADO_COMPLETADA, Cita::ESTADO_AUSENTE])) {
                $estadoAleatorio = Cita::ESTADO_CONFIRMADA;
            }

            $hora = rand(8, 16);
            $minuto = rand(0, 1) == 0 ? '00' : '30';
            $horaInicio = sprintf("%02d:%s", $hora, $minuto);
            $horaFin = sprintf("%02d:%s", $hora + 1, $minuto);

            $cita = Cita::create([
                'paciente_id' => $paciente->id,
                'especialista_id' => $especialista->id,
                'fecha' => $fechaAleatoria->toDateString(),
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'estado' => $estadoAleatorio,
                'motivo' => 'Consulta generada (Datos Tesis)',
                'registrado_por' => $admin->id,
            ]);

            // Crear Historia Clínica si está completada
            if ($estadoAleatorio === Cita::ESTADO_COMPLETADA) {
                HistoriaClinica::create([
                    'paciente_id' => $paciente->id,
                    'especialista_id' => $especialista->id,
                    'cita_id' => $cita->id,
                    'fecha_consulta' => $fechaAleatoria->toDateString(),
                    'motivo_consulta' => 'Control regular',
                    'evaluacion' => 'El paciente reporta mejoría. Examen físico dentro de la normalidad.',
                    'tratamiento_aplicado' => 'Sesión de terapia de 45 mins.',
                    'creado_por' => $especialista->user_id,
                ]);
            }
        }
        
        $this->command->info('Se han generado citas enfocadas en el mes actual para enriquecer los gráficos.');
    }
}
