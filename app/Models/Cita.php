<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'citas';

    /**
     * Estados posibles de una cita.
     */
    const ESTADO_PENDIENTE    = 'pendiente';
    const ESTADO_CONFIRMADA   = 'confirmada';
    const ESTADO_COMPLETADA   = 'completada';
    const ESTADO_CANCELADA    = 'cancelada';
    const ESTADO_AUSENTE      = 'ausente';
    const ESTADO_REPROGRAMADA = 'reprogramada';

    protected $fillable = [
        'paciente_id',
        'especialista_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'motivo',
        'notas_recepcion',
        'cita_reprogramada_id',
        'registrado_por',
        'cancelado_por',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // -----------------------------------------------------------------------
    // Scopes de consulta
    // -----------------------------------------------------------------------

    /**
     * Citas pendientes o confirmadas (activas).
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereIn('estado', [self::ESTADO_PENDIENTE, self::ESTADO_CONFIRMADA]);
    }

    /**
     * Citas del día actual.
     */
    public function scopeDeHoy(Builder $query): Builder
    {
        return $query->whereDate('fecha', today());
    }

    /**
     * Citas marcadas como ausentismo.
     */
    public function scopeAusentes(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_AUSENTE);
    }

    /**
     * Verifica si existe un cruce de horario para un especialista dado.
     * Útil para validar duplicidad antes de registrar una nueva cita.
     */
    public static function existeCruceHorario(
        int $especialistaId,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $excluirCitaId = null
    ): bool {
        return self::where('especialista_id', $especialistaId)
            ->whereDate('fecha', $fecha)
            ->whereNotIn('estado', [self::ESTADO_CANCELADA, self::ESTADO_REPROGRAMADA])
            ->where(function (Builder $q) use ($horaInicio, $horaFin) {
                // Se cruzan si el inicio de una cita cae dentro del rango de otra
                $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                  ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                  ->orWhere(function (Builder $q2) use ($horaInicio, $horaFin) {
                      $q2->where('hora_inicio', '<=', $horaInicio)
                         ->where('hora_fin', '>=', $horaFin);
                  });
            })
            ->when($excluirCitaId, fn (Builder $q) => $q->where('id', '!=', $excluirCitaId))
            ->exists();
    }

    /**
     * Verifica si la cita está dentro del horario laboral del especialista.
     * Retorna true si es válido, o un string con el mensaje de error si no lo es.
     */
    public static function esHorarioValido(int $especialistaId, string $fecha, string $horaInicio, string $horaFin): bool|string
    {
        $especialista = Especialista::find($especialistaId);
        if (!$especialista) return 'Especialista no encontrado.';

        // 1. Validar si el día de la semana es laborable para él
        // dayOfWeekIso devuelve 1 (Lunes) a 7 (Domingo)
        $diaSemana = \Carbon\Carbon::parse($fecha)->dayOfWeekIso; 
        $diasLaborables = $especialista->dias_laborables ?? [];
        
        if (!in_array((string)$diaSemana, $diasLaborables) && !in_array($diaSemana, $diasLaborables)) {
            return 'El especialista no labora en el día seleccionado.';
        }

        // 2. Validar que la cita esté dentro del rango de entrada y salida
        // Asumiendo que horaInicio y horaFin son "HH:mm" y las BD guardan "HH:mm:ss"
        $hInicio = substr($horaInicio, 0, 5);
        $hFin = substr($horaFin, 0, 5);
        $hEntrada = substr($especialista->hora_entrada, 0, 5);
        $hSalida = substr($especialista->hora_salida, 0, 5);

        if ($hInicio < $hEntrada || $hFin > $hSalida) {
            return "El horario seleccionado está fuera del turno del especialista ({$hEntrada} a {$hSalida}).";
        }

        return true;
    }

    // -----------------------------------------------------------------------
    // Relaciones
    // -----------------------------------------------------------------------

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function especialista(): BelongsTo
    {
        return $this->belongsTo(Especialista::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    /**
     * Si la cita fue reprogramada, la nueva cita resultante.
     */
    public function citaReprogramada(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'cita_reprogramada_id');
    }

    /**
     * Evolución clínica asociada a esta cita (si la hay).
     */
    public function evolucion(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EvolucionClinica::class);
    }
}
