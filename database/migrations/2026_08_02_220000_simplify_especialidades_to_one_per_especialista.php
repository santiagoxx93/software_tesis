<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar especialidad_id a especialistas
        Schema::table('especialistas', function (Blueprint $table) {
            $table->foreignId('especialidad_id')->nullable()->after('apellidos')
                  ->constrained('especialidades')->nullOnDelete();
        });

        // 2. Migrar datos: tomar la primera especialidad de cada especialista desde el pivote
        $pivots = DB::table('especialidad_especialista')->get();
        $asignados = [];
        foreach ($pivots as $pivot) {
            // Solo la primera especialidad (evitar duplicados)
            if (!in_array($pivot->especialista_id, $asignados)) {
                DB::table('especialistas')
                    ->where('id', $pivot->especialista_id)
                    ->update(['especialidad_id' => $pivot->especialidad_id]);
                $asignados[] = $pivot->especialista_id;
            }
        }

        // 3. Eliminar la tabla pivote
        Schema::dropIfExists('especialidad_especialista');
    }

    public function down(): void
    {
        // Recrear la tabla pivote
        Schema::create('especialidad_especialista', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialidad_id')->constrained('especialidades')->onDelete('cascade');
            $table->foreignId('especialista_id')->constrained('especialistas')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['especialidad_id', 'especialista_id']);
        });

        // Eliminar la columna directa
        Schema::table('especialistas', function (Blueprint $table) {
            $table->dropForeign(['especialidad_id']);
            $table->dropColumn('especialidad_id');
        });
    }
};
