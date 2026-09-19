<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cita\FiltrarCitasRequest;
use App\Http\Requests\Cita\StoreCitaRequest;
use App\Http\Requests\Cita\UpdateCitaRequest;
use App\Http\Requests\Cita\UpdateEstadoCitaRequest;
use App\Http\Resources\CitaResource;
use App\Models\Cita;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;

class CitaController extends Controller
{
    public function __construct(private readonly CitaService $citas) {}

    public function index(FiltrarCitasRequest $request): JsonResponse
    {
        $citas = $this->citas->listar($request->validated());

        return CitaResource::collection($citas)->response();
    }

    public function store(StoreCitaRequest $request): JsonResponse
    {
        $cita = $this->citas->crear($request->validated());

        return (new CitaResource($cita))->response()->setStatusCode(201);
    }

    public function show(Cita $cita): JsonResponse
    {
        return (new CitaResource($cita->load(['paciente', 'doctor'])))->response();
    }

    public function update(UpdateCitaRequest $request, Cita $cita): JsonResponse
    {
        $cita = $this->citas->reprogramar($cita, $request->validated());

        return (new CitaResource($cita))->response();
    }

    public function cambiarEstado(UpdateEstadoCitaRequest $request, Cita $cita): JsonResponse
    {
        $cita = $this->citas->cambiarEstado($cita, $request->validated()['estado']);

        return (new CitaResource($cita))->response();
    }
}
