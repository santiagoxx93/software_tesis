<?php
use App\Models\User;
use App\Models\Paciente;
use App\Models\HistoriaClinica;
use App\Models\Especialista;
use Carbon\Carbon;

$faker = \Faker\Factory::create('es_ES');

$especialistas = Especialista::all();
if($especialistas->isEmpty()) {
    echo 'No hay especialistas.';
    exit;
}

// Crear 20 pacientes falsos si no hay suficientes
$count = Paciente::count();
if ($count < 20) {
    for ($i=0; $i<(20-$count); $i++) {
        Paciente::create([
            'nombres' => $faker->firstName,
            'apellidos' => $faker->lastName . ' ' . $faker->lastName,
            'cedula' => $faker->unique()->randomNumber(8),
            'fecha_nacimiento' => $faker->date('Y-m-d', '-20 years'),
            'telefono' => '0414' . $faker->randomNumber(7, true),
            'email' => $faker->unique()->safeEmail,
            'direccion' => $faker->address,
            'sexo' => $faker->randomElement(['M', 'F']),
            'estado_civil' => $faker->randomElement(['Soltero', 'Casado', 'Divorciado']),
            'ocupacion' => $faker->jobTitle,
        ]);
    }
}
$pacientes = Paciente::all();
echo "Pacientes listos. \n";

// Crear 300 historias clinicas en los ultimos 6 meses
for ($i=0; $i<300; $i++) {
    $paciente = $pacientes->random();
    $especialista = $especialistas->random();
    
    // Fecha aleatoria en los ultimos 180 dias
    $fecha = Carbon::now()->subDays(rand(0, 180));

    HistoriaClinica::create([
        'paciente_id' => $paciente->id,
        'especialista_id' => $especialista->id,
        'fecha_consulta' => $fecha->format('Y-m-d'),
        'motivo_consulta' => $faker->sentence(),
        'evaluacion' => $faker->paragraph(),
        'tratamiento_aplicado' => $faker->paragraph(),
        'observaciones_iniciales' => $faker->sentence(),
        'creado_por' => $especialista->user_id,
    ]);
}

echo "Historias clínicas creadas con éxito.";
