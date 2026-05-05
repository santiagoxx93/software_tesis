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
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('restrict');
            // Datos de antecedentes generales del paciente (solo especialista puede ver/editar)
            $table->text('antecedentes_personales')->nullable()->comment('Enfermedades previas, cirugías, alergias');
            $table->text('antecedentes_familiares')->nullable()->comment('Historial familiar relevante');
            $table->text('motivo_consulta')->nullable()->comment('Motivo principal de la primera consulta');
            $table->string('grupo_sanguineo', 10)->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->text('observaciones_iniciales')->nullable();
            // Metadatos de creación
            $table->foreignId('creado_por')->constrained('users')->comment('Usuario que creó la historia');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
