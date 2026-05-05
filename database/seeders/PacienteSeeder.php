<?php

namespace Database\Seeders;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('rol', 'admin')->first();

        $pacientes = [
            [
                'cedula'              => 'V-10234567',
                'nombres'             => 'Ana Lucía',
                'apellidos'           => 'Pérez Mora',
                'fecha_nacimiento'    => '1985-03-15',
                'sexo'                => 'F',
                'telefono'            => '0412-111-2233',
                'email'               => 'ana.perez@email.com',
                'direccion'           => 'Urb. Las Mercedes, Calle 5, Casa 12, Caracas',
                'ocupacion'           => 'Docente',
            ],
            [
                'cedula'              => 'V-15678901',
                'nombres'             => 'Roberto',
                'apellidos'           => 'Salazar Díaz',
                'fecha_nacimiento'    => '1972-07-22',
                'sexo'                => 'M',
                'telefono'            => '0416-333-4455',
                'email'               => 'roberto.salazar@email.com',
                'direccion'           => 'Res. El Bosque, Torre A, Piso 3, Apto 3B, Valencia',
                'ocupacion'           => 'Contador',
            ],
            [
                'cedula'              => 'V-20345678',
                'nombres'             => 'Carmen',
                'apellidos'           => 'Torres Herrera',
                'fecha_nacimiento'    => '1990-11-08',
                'sexo'                => 'F',
                'telefono'            => '0424-555-6677',
                'telefono_emergencia' => '0414-777-8899',
                'direccion'           => 'Calle Principal, Local 4, Maracay',
                'ocupacion'           => 'Enfermera',
            ],
            [
                'cedula'              => 'V-08765432',
                'nombres'             => 'José Miguel',
                'apellidos'           => 'Fuentes Blanco',
                'fecha_nacimiento'    => '1965-01-30',
                'sexo'                => 'M',
                'telefono'            => '0412-999-0011',
                'direccion'           => 'Quinta Los Pinos, San Cristóbal',
                'ocupacion'           => 'Ingeniero Civil',
            ],
            [
                'cedula'              => 'V-25901234',
                'nombres'             => 'Gabriela',
                'apellidos'           => 'Morales Castillo',
                'fecha_nacimiento'    => '1998-05-17',
                'sexo'                => 'F',
                'telefono'            => '0426-123-4567',
                'email'               => 'gaby.morales@email.com',
                'ocupacion'           => 'Estudiante',
            ],
            [
                'cedula'              => 'V-11223344',
                'nombres'             => 'Luis Alberto',
                'apellidos'           => 'Vargas Quintero',
                'fecha_nacimiento'    => '1978-09-03',
                'sexo'                => 'M',
                'telefono'            => '0414-234-5678',
                'telefono_emergencia' => '0412-876-5432',
                'direccion'           => 'Av. Bolívar, Edificio Central, Piso 5, Barquisimeto',
                'ocupacion'           => 'Abogado',
            ],
            [
                'cedula'              => 'V-18765432',
                'nombres'             => 'Sofía',
                'apellidos'           => 'Mendez Ríos',
                'fecha_nacimiento'    => '1955-12-25',
                'sexo'                => 'F',
                'telefono'            => '0416-345-6789',
                'ocupacion'           => 'Jubilada',
            ],
            [
                'cedula'              => 'V-22334455',
                'nombres'             => 'Andrés',
                'apellidos'           => 'Guerrero López',
                'fecha_nacimiento'    => '1988-04-11',
                'sexo'                => 'M',
                'telefono'            => '0424-456-7890',
                'email'               => 'andres.guerrero@email.com',
                'ocupacion'           => 'Empresario',
            ],
            [
                'cedula'              => 'V-30123456',
                'nombres'             => 'Patricia',
                'apellidos'           => 'Jiménez Suárez',
                'fecha_nacimiento'    => '2001-08-29',
                'sexo'                => 'F',
                'telefono'            => '0412-567-8901',
                'ocupacion'           => 'Universitaria',
            ],
            [
                'cedula'              => 'V-13579246',
                'nombres'             => 'Eduardo',
                'apellidos'           => 'Castro Núñez',
                'fecha_nacimiento'    => '1960-06-14',
                'sexo'                => 'M',
                'telefono'            => '0416-678-9012',
                'telefono_emergencia' => '0424-012-3456',
                'ocupacion'           => 'Médico',
            ],
        ];

        foreach ($pacientes as $datos) {
            $paciente = Paciente::create($datos);

            // Crear historia clínica vacía para cada paciente
            HistoriaClinica::create([
                'paciente_id'          => $paciente->id,
                'motivo_consulta'      => 'Primera consulta de reflexología podal.',
                'observaciones_iniciales' => 'Paciente referido al centro por recomendación.',
                'creado_por'           => $admin->id,
            ]);
        }
    }
}
