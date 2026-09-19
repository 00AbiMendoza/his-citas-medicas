<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Las respuestas de la API deben usar 400 para datos invalidos (RQNF-03),
     * no el 422 que Laravel usa por defecto.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Los datos proporcionados no son validos.',
            'errors' => $validator->errors(),
        ], 400));
    }
}
