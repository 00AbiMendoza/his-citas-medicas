<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PacienteResource;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;

class PacienteController extends Controller
{
    public function index(): JsonResponse
    {
        $pacientes = Paciente::orderBy('nombre')->orderBy('apellido')->get();

        return PacienteResource::collection($pacientes)->response();
    }
}
