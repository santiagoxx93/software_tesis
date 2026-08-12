<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrar datos existentes (crear especialidades y llenar pivote)
        $especialistas = \Illuminate\Support\Facades\DB::table('especialistas')->get();
        
        foreach ($especialistas as $esp) {
            if (!empty($esp->especialidad)) {
                // Separar por " y " o "," si hubiese múltiples en el texto viejo
                $nombresEspecialidades = array_map('trim', preg_split('/( y |,)/i', $esp->especialidad));
                
                foreach ($nombresEspecialidades as $nombre) {
                    if (empty($nombre)) continue;
                    
                    // Buscar o crear la especialidad
                    $especialidad = \Illuminate\Support\Facades\DB::table('especialidades')
                        ->where('nombre', $nombre)->first();
                        
                    if (!$especialidad) {
                        $id = \Illuminate\Support\Facades\DB::table('especialidades')->insertGetId([
                            'nombre' => $nombre,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $especialidad_id = $id;
                    } else {
                        $especialidad_id = $especialidad->id;
                    }
                    
                    // Insertar en la tabla pivote
                    \Illuminate\Support\Facades\DB::table('especialidad_especialista')->insertOrIgnore([
                        'especialidad_id' => $especialidad_id,
                        'especialista_id' => $esp->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Eliminar la columna vieja
        Schema::table('especialistas', function (Blueprint $table) {
            $table->dropColumn('especialidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('especialistas', function (Blueprint $table) {
            $table->string('especialidad', 150)->default('Reflexología Podal');
        });
        
        // No se deshace la migración de datos hacia atrás (es destructivo)
    }
};
