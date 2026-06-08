<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear roles de Spatie
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'especialista']);

        // 2. Asignar roles a usuarios existentes
        $users = User::all();
        foreach ($users as $user) {
            // Usa directamente el atributo original por si acaso el modelo ya no tiene el helper o cast
            $oldRole = $user->getRawOriginal('rol');
            if ($oldRole && in_array($oldRole, ['admin', 'especialista'])) {
                $user->assignRole($oldRole);
            }
        }

        // 3. Eliminar la columna antigua 'rol'
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restaurar la columna 'rol'
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol')->default('especialista');
        });

        // 2. Restaurar datos desde Spatie hacia la columna
        $users = User::all();
        foreach ($users as $user) {
            if ($user->hasRole('admin')) {
                $user->update(['rol' => 'admin']);
            } elseif ($user->hasRole('especialista')) {
                $user->update(['rol' => 'especialista']);
            }
        }
    }
};
