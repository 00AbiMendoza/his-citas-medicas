<?php

namespace Database\Seeders;

use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pacientes = [
            ['nombre' => 'Luis', 'apellido' => 'Fernández', 'telefono' => '5555-1001', 'email' => 'luis.fernandez@example.com'],
            ['nombre' => 'Sofía', 'apellido' => 'Martínez', 'telefono' => '5555-1002', 'email' => 'sofia.martinez@example.com'],
            ['nombre' => 'Pedro', 'apellido' => 'Castillo', 'telefono' => '5555-1003', 'email' => 'pedro.castillo@example.com'],
            ['nombre' => 'Valeria', 'apellido' => 'Morales', 'telefono' => '5555-1004', 'email' => 'valeria.morales@example.com'],
            ['nombre' => 'Diego', 'apellido' => 'Hernández', 'telefono' => '5555-1005', 'email' => 'diego.hernandez@example.com'],
            ['nombre' => 'Camila', 'apellido' => 'Torres', 'telefono' => '5555-1006', 'email' => 'camila.torres@example.com'],
        ];

        foreach ($pacientes as $paciente) {
            Paciente::create($paciente);
        }
    }
}
