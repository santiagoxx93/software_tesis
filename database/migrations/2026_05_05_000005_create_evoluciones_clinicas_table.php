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
        Schema::create('evoluciones_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('restrict');
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete()
                ->comment('Cita asociada a esta evolución (opcional)');
            $table->foreignId('especialista_id')->constrained('especialistas')->onDelete('restrict')
                ->comment('Especialista que registra la evolución');
            $table->date('fecha_consulta');
            $table->text('evaluacion')->comment('Evaluación clínica realizada en la sesión');
            $table->text('tratamiento_aplicado')->comment('Técnicas y zonas de reflexología tratadas');
            $table->text('respuesta_paciente')->nullable()->comment('Cómo respondió el paciente al tratamiento');
            $table->text('plan_siguiente_sesion')->nullable()->comment('Indicaciones para la próxima sesión');
            // Protección de integridad: las evoluciones no deben ser modificadas una vez creadas
            $table->timestamp('bloqueado_en')->nullable()
                ->comment('Si tiene valor, el registro está bloqueado contra ediciones');
            $table->timestamps();
            // Sin softDeletes para garantizar trazabilidad e integridad del expediente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evoluciones_clinicas');
    }
};
