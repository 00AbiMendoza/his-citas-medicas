<?php

namespace App\Http\Requests\Cita;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateCitaRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'paciente_id' => ['sometimes', 'integer', Rule::exists('pacientes', 'id')],
            'doctor_id' => ['sometimes', 'integer', Rule::exists('doctores', 'id')],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
            'motivo' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.exists' => 'El paciente indicado no existe.',
            'doctor_id.exists' => 'El doctor indicado no existe.',
            'fecha_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
