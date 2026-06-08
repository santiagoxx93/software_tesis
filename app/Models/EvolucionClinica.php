<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EvolucionClinica extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'evoluciones_clinicas';

    /**
     * Las evoluciones clínicas NO tienen SoftDeletes para garantizar
     * la trazabilidad e integridad del expediente médico.
     */

    protected $fillable = [
        'historia_clinica_id',
        'cita_id',
        'especialista_id',
        'fecha_consulta',
        'evaluacion',
        'tratamiento_aplicado',
        'respuesta_paciente',
        'plan_siguiente_sesion',
        'bloqueado_en',
    ];

    protected $casts = [
        'fecha_consulta' => 'date',
        'bloqueado_en'   => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty();
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Indica si el registro está bloqueado contra ediciones.
     */
    public function estaBloqueado(): bool
    {
        return $this->bloqueado_en !== null;
    }

    /**
     * Bloquea el registro para proteger su integridad.
     * Una vez bloqueado no debe poder editarse.
     */
    public function bloquear(): void
    {
        if (! $this->estaBloqueado()) {
            $this->update(['bloqueado_en' => now()]);
        }
    }

    // -----------------------------------------------------------------------
    // Relaciones
    // -----------------------------------------------------------------------

    /**
     * Historia clínica a la que pertenece esta evolución.
     */
    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    /**
     * Cita relacionada con esta evolución (puede ser nula).
     */
    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    /**
     * Especialista que registró la evolución.
     */
    public function especialista(): BelongsTo
    {
        return $this->belongsTo(Especialista::class);
    }
}
