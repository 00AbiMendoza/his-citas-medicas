<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctores = [
            ['nombre' => 'Juan', 'apellido' => 'Pérez', 'especialidad' => 'Medicina General', 'telefono' => '5555-2001', 'email' => 'juan.perez@his.local'],
            ['nombre' => 'María', 'apellido' => 'López', 'especialidad' => 'Pediatría', 'telefono' => '5555-2002', 'email' => 'maria.lopez@his.local'],
            ['nombre' => 'Carlos', 'apellido' => 'Ramírez', 'especialidad' => 'Cardiología', 'telefono' => '5555-2003', 'email' => 'carlos.ramirez@his.local'],
            ['nombre' => 'Ana', 'apellido' => 'Gómez', 'especialidad' => 'Dermatología', 'telefono' => '5555-2004', 'email' => 'ana.gomez@his.local'],
        ];

        foreach ($doctores as $doctor) {
            Doctor::create($doctor);
        }
    }
}
