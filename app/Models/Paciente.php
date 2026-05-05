<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'telefono',
        'telefono_emergencia',
        'email',
        'direccion',
        'ocupacion',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // -----------------------------------------------------------------------
    // Accesores
    // -----------------------------------------------------------------------

    /**
     * Nombre completo del paciente.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /**
     * Edad calculada a partir de la fecha de nacimiento.
     */
    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }

    // -----------------------------------------------------------------------
    // Relaciones
    // -----------------------------------------------------------------------

    /**
     * Historia clínica del paciente (una sola por paciente).
     */
    public function historiaClinica(): HasOne
    {
        return $this->hasOne(HistoriaClinica::class);
    }

    /**
     * Todas las citas del paciente.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
