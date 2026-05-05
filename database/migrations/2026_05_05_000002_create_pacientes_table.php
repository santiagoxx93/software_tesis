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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 20)->unique()->comment('Número de cédula de identidad');
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F', 'Otro'])->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('telefono_emergencia', 20)->nullable()->comment('Contacto de emergencia');
            $table->string('email', 150)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ocupacion', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
