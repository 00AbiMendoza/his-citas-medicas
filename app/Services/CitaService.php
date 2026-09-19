<?php

namespace App\Services;

use App\Enums\EstadoCita;
use App\Exceptions\CitaConflictoException;
use App\Models\Cita;
use Illuminate\Database\Eloquent\Collection;

class CitaService
{
    /**
     * @param  array{doctor_id?: int, paciente_id?: int, desde?: string, hasta?: string}  $filtros
     */
    public function listar(array $filtros): Collection
    {
        $query = Cita::with(['paciente', 'doctor']);

        if (! empty($filtros['doctor_id'])) {
            $query->where('doctor_id', $filtros['doctor_id']);
        }

        if (! empty($filtros['paciente_id'])) {
            $query->where('paciente_id', $filtros['paciente_id']);
        }

        if (! empty($filtros['desde'])) {
            $query->where('fecha_fin', '>=', $filtros['desde']);
        }

        if (! empty($filtros['hasta'])) {
            $query->where('fecha_inicio', '<=', $filtros['hasta']);
        }

        return $query->orderBy('fecha_inicio')->get();
    }

    /**
     * @param  array{paciente_id: int, doctor_id: int, fecha_inicio: string, fecha_fin: string, motivo: string}  $datos
     */
    public function crear(array $datos): Cita
    {
        $datos['estado'] ??= EstadoCita::Pendiente->value;

        $this->verificarDisponibilidad($datos['doctor_id'], $datos['fecha_inicio'], $datos['fecha_fin']);

        $cita = Cita::create($datos);

        return $cita->load(['paciente', 'doctor']);
    }

    /**
     * @param  array{paciente_id?: int, doctor_id?: int, fecha_inicio: string, fecha_fin: string, motivo?: string}  $datos
     */
    public function reprogramar(Cita $cita, array $datos): Cita
    {
        $doctorId = $datos['doctor_id'] ?? $cita->doctor_id;

        $this->verificarDisponibilidad($doctorId, $datos['fecha_inicio'], $datos['fecha_fin'], excluirCitaId: $cita->id);

        $cita->update($datos);

        return $cita->fresh(['paciente', 'doctor']);
    }

    public function cambiarEstado(Cita $cita, string $estado): Cita
    {
        $estadoActual = $cita->estado;
        $estadoNuevo = EstadoCita::from($estado);

        if (! $estadoActual->puedeTransicionarA($estadoNuevo)) {
            throw new CitaConflictoException(
                "No se puede cambiar el estado de '{$estadoActual->etiqueta()}' a '{$estadoNuevo->etiqueta()}'."
            );
        }

        $cita->update(['estado' => $estadoNuevo->value]);

        return $cita->fresh(['paciente', 'doctor']);
    }

    /**
     * Valida en el servidor que no exista otra cita activa que se solape
     * en horario para el mismo doctor (RQF-03, RQNF-07).
     */
    private function verificarDisponibilidad(
        int $doctorId,
        string $fechaInicio,
        string $fechaFin,
        ?int $excluirCitaId = null,
    ): void {
        $existeConflicto = Cita::where('doctor_id', $doctorId)
            ->where('estado', '!=', EstadoCita::Cancelada->value)
            ->where('fecha_inicio', '<', $fechaFin)
            ->where('fecha_fin', '>', $fechaInicio)
            ->when($excluirCitaId, fn ($query) => $query->where('id', '!=', $excluirCitaId))
            ->exists();

        if ($existeConflicto) {
            throw new CitaConflictoException(
                'Ya existe una cita activa para este doctor en el horario indicado.'
            );
        }
    }
}
