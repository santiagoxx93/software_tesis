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
        Schema::table('especialistas', function (Blueprint $table) {
            $table->time('hora_entrada')->default('08:00:00')->after('especialidad');
            $table->time('hora_salida')->default('17:00:00')->after('hora_entrada');
            $table->json('dias_laborables')->nullable()->after('hora_salida');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('especialistas', function (Blueprint $table) {
            $table->dropColumn(['hora_entrada', 'hora_salida', 'dias_laborables']);
        });
    }
};
