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
        // First drop the evoluciones_clinicas table
        Schema::dropIfExists('evoluciones_clinicas');

        // Modify historias_clinicas
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->foreignId('especialista_id')->nullable()->constrained('especialistas')->nullOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            
            $table->date('fecha_consulta')->nullable();
            $table->text('evaluacion')->nullable();
            $table->text('tratamiento_aplicado')->nullable();
            $table->text('respuesta_paciente')->nullable();
            $table->text('plan_siguiente_sesion')->nullable();
            $table->timestamp('bloqueado_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->dropForeign(['especialista_id']);
            $table->dropForeign(['cita_id']);
            
            $table->dropColumn([
                'especialista_id',
                'cita_id',
                'fecha_consulta',
                'evaluacion',
                'tratamiento_aplicado',
                'respuesta_paciente',
                'plan_siguiente_sesion',
                'bloqueado_en',
            ]);
        });

        // Recreate the table in the down method if needed
        Schema::create('evoluciones_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->restrictOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->foreignId('especialista_id')->constrained('especialistas')->restrictOnDelete();
            $table->date('fecha_consulta');
            $table->text('evaluacion');
            $table->text('tratamiento_aplicado');
            $table->text('respuesta_paciente')->nullable();
            $table->text('plan_siguiente_sesion')->nullable();
            $table->timestamp('bloqueado_en')->nullable();
            $table->timestamps();
        });
    }
};
