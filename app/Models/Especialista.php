<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Especialista extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'especialistas';

    protected $fillable = [
        'user_id',
        'cedula',
        'nombres',
        'apellidos',
        'especialidad',
        'telefono',
        'hora_entrada',
        'hora_salida',
        'dias_laborables',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'dias_laborables' => 'array',
    ];

    // -----------------------------------------------------------------------
    // Accesores
    // -----------------------------------------------------------------------

    /**
     * Nombre completo del especialista.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    // -----------------------------------------------------------------------
    // Relaciones
    // -----------------------------------------------------------------------

    /**
     * Cuenta de usuario del sistema asociada a este especialista.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Citas asignadas a este especialista.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * Evoluciones clínicas registradas por este especialista.
     */
    public function evolucionesClincias(): HasMany
    {
        return $this->hasMany(EvolucionClinica::class);
    }
}
