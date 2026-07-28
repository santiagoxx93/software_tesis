<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class HistoriaClinica extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'paciente_id',
        'especialista_id',
        'cita_id',
        'fecha_consulta',
        'antecedentes_personales',
        'antecedentes_familiares',
        'motivo_consulta',
        'grupo_sanguineo',
        'medicamentos_actuales',
        'observaciones_iniciales',
        'evaluacion',
        'tratamiento_aplicado',
        'respuesta_paciente',
        'plan_siguiente_sesion',
        'bloqueado_en',
        'creado_por',
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
     * Paciente al que pertenece esta historia clínica.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Especialista que registró la historia/visita.
     */
    public function especialista(): BelongsTo
    {
        return $this->belongsTo(Especialista::class);
    }

    /**
     * Cita relacionada con esta historia/visita (puede ser nula).
     */
    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    /**
     * Usuario que creó la historia clínica.
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
