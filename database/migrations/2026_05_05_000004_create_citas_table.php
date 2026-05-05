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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('restrict');
            $table->foreignId('especialista_id')->constrained('especialistas')->onDelete('restrict');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            // Estados del ciclo de vida de la cita
            $table->enum('estado', [
                'pendiente',     // Registrada, sin confirmar
                'confirmada',    // Asistencia confirmada por el paciente
                'completada',    // Atendida exitosamente
                'cancelada',     // Cancelada por el paciente o el centro
                'ausente',       // Paciente no se presentó (ausentismo)
                'reprogramada',  // Reprogramada a otra fecha/hora
            ])->default('pendiente');
            $table->text('motivo')->nullable()->comment('Motivo o razón de la cita');
            $table->text('notas_recepcion')->nullable()->comment('Notas del personal de recepción (solo admin ve esto)');
            // Si fue reprogramada, referencia a la nueva cita
            $table->foreignId('cita_reprogramada_id')->nullable()->constrained('citas')->nullOnDelete()
                ->comment('ID de la nueva cita si esta fue reprogramada');
            // Usuario que registró y/o canceló la cita
            $table->foreignId('registrado_por')->constrained('users');
            $table->foreignId('cancelado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índice para la validación de duplicidad de horarios por especialista
            $table->index(['especialista_id', 'fecha', 'hora_inicio'], 'idx_citas_especialista_horario');
            $table->index(['paciente_id', 'fecha'], 'idx_citas_paciente_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
