<?php

namespace App\Http\Requests\Cita;

use App\Enums\EstadoCita;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoCitaRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'estado' => ['required', 'string', Rule::enum(EstadoCita::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser uno de: pendiente, confirmada, cancelada, atendida.',
        ];
    }
}
