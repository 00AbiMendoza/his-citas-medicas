<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paciente_id' => $this->paciente_id,
            'doctor_id' => $this->doctor_id,
            'paciente' => [
                'id' => $this->paciente->id,
                'nombre_completo' => $this->paciente->nombre_completo,
                'telefono' => $this->paciente->telefono,
                'email' => $this->paciente->email,
            ],
            'doctor' => [
                'id' => $this->doctor->id,
                'nombre_completo' => $this->doctor->nombre_completo,
                'especialidad' => $this->doctor->especialidad,
            ],
            'fecha_inicio' => $this->fecha_inicio?->toIso8601String(),
            'fecha_fin' => $this->fecha_fin?->toIso8601String(),
            'motivo' => $this->motivo,
            'estado' => $this->estado,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
