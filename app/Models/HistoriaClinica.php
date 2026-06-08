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
        'antecedentes_personales',
        'antecedentes_familiares',
        'motivo_consulta',
        'grupo_sanguineo',
        'medicamentos_actuales',
        'observaciones_iniciales',
        'creado_por',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty();
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
     * Usuario que creó la historia clínica.
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Entradas de evolución clínica de esta historia, ordenadas cronológicamente.
     */
    public function evoluciones(): HasMany
    {
        return $this->hasMany(EvolucionClinica::class)->orderBy('fecha_consulta', 'asc');
    }
}
