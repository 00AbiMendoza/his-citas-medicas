<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    public function index(): JsonResponse
    {
        $doctores = Doctor::orderBy('nombre')->orderBy('apellido')->get();

        return DoctorResource::collection($doctores)->response();
    }
}
