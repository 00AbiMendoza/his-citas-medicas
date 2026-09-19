<?php

namespace App\Services;

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
        $datos['estado'] ??= 'pendiente';

        $cita = Cita::create($datos);

        return $cita->load(['paciente', 'doctor']);
    }

    /**
     * @param  array{paciente_id?: int, doctor_id?: int, fecha_inicio: string, fecha_fin: string, motivo?: string}  $datos
     */
    public function reprogramar(Cita $cita, array $datos): Cita
    {
        $cita->update($datos);

        return $cita->fresh(['paciente', 'doctor']);
    }

    public function cambiarEstado(Cita $cita, string $estado): Cita
    {
        $cita->update(['estado' => $estado]);

        return $cita->fresh(['paciente', 'doctor']);
    }
}
