<?php

namespace App\Http\Requests\Cita;

use App\Http\Requests\ApiFormRequest;

class FiltrarCitasRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'doctor_id' => ['sometimes', 'integer'],
            'paciente_id' => ['sometimes', 'integer'],
            'desde' => ['sometimes', 'date'],
            'hasta' => ['sometimes', 'date'],
        ];
    }
}
