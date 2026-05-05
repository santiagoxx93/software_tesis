<?php

namespace Database\Seeders;

use App\Models\Especialista;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- Admin (Recepción) ---
        User::create([
            'name'     => 'Recepción San Alfonso',
            'email'    => 'admin@santalfonso.com',
            'password' => Hash::make('password'),
            'rol'      => 'admin',
            'activo'   => true,
        ]);

        // --- Especialista (Terapeuta) ---
        $terapeuta = User::create([
            'name'     => 'Dra. María González',
            'email'    => 'terapeuta@santalfonso.com',
            'password' => Hash::make('password'),
            'rol'      => 'especialista',
            'activo'   => true,
        ]);

        // Perfil de especialista vinculado al usuario
        Especialista::create([
            'user_id'      => $terapeuta->id,
            'cedula'       => 'V-12345678',
            'nombres'      => 'María',
            'apellidos'    => 'González',
            'especialidad' => 'Reflexología Podal',
            'telefono'     => '0412-555-0001',
            'activo'       => true,
        ]);

        // --- Segundo Especialista ---
        $terapeuta2 = User::create([
            'name'     => 'Dr. Carlos Ramírez',
            'email'    => 'terapeuta2@santalfonso.com',
            'password' => Hash::make('password'),
            'rol'      => 'especialista',
            'activo'   => true,
        ]);

        Especialista::create([
            'user_id'      => $terapeuta2->id,
            'cedula'       => 'V-98765432',
            'nombres'      => 'Carlos',
            'apellidos'    => 'Ramírez',
            'especialidad' => 'Reflexología Podal y Acupresión',
            'telefono'     => '0414-555-0002',
            'activo'       => true,
        ]);
    }
}
