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
        $admin = User::create([
            'name'     => 'Recepción San Alfonso',
            'email'    => 'admin@santalfonso.com',
            'password' => Hash::make('password'),
            'activo'   => true,
        ]);
        $admin->assignRole('admin');

        // --- Especialista (Terapeuta) ---
        $terapeuta = User::create([
            'name'     => 'Dra. María González',
            'email'    => 'terapeuta@santalfonso.com',
            'password' => Hash::make('password'),
            'activo'   => true,
        ]);
        $terapeuta->assignRole('especialista');

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
            'activo'   => true,
        ]);
        $terapeuta2->assignRole('especialista');

        Especialista::create([
            'user_id'      => $terapeuta2->id,
            'cedula'       => 'V-98765432',
            'nombres'      => 'Carlos',
            'apellidos'    => 'Ramírez',
            'especialidad' => 'Reflexología Podal y Acupresión',
            'telefono'     => '0414-555-0002',
            'activo'       => true,
        ]);

        // --- Tercer Especialista ---
        $terapeuta3 = User::create([
            'name'     => 'Dra. Johalys Rangel',
            'email'    => 'johalys@santalfonso.com',
            'password' => Hash::make('password'),
            'activo'   => true,
        ]);
        $terapeuta3->assignRole('especialista');

        Especialista::create([
            'user_id'      => $terapeuta3->id,
            'cedula'       => 'V-20123456',
            'nombres'      => 'Johalys',
            'apellidos'    => 'Rangel',
            'especialidad' => 'Terapia Holística',
            'telefono'     => '0412-555-0003',
            'activo'       => true,
        ]);
    }
}
