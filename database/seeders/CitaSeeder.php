<?php

namespace Database\Seeders;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hoy = Carbon::today();

        $citas = [
            ['paciente_id' => 1, 'doctor_id' => 1, 'dia' => 2, 'hora' => '09:00', 'minutos' => 30, 'motivo' => 'Consulta general', 'estado' => 'pendiente'],
            ['paciente_id' => 2, 'doctor_id' => 1, 'dia' => 3, 'hora' => '10:00', 'minutos' => 30, 'motivo' => 'Control de presión arterial', 'estado' => 'confirmada'],
            ['paciente_id' => 3, 'doctor_id' => 2, 'dia' => 2, 'hora' => '11:00', 'minutos' => 30, 'motivo' => 'Chequeo pediátrico', 'estado' => 'pendiente'],
            ['paciente_id' => 4, 'doctor_id' => 2, 'dia' => -2, 'hora' => '09:00', 'minutos' => 45, 'motivo' => 'Vacunación', 'estado' => 'atendida'],
            ['paciente_id' => 5, 'doctor_id' => 3, 'dia' => 3, 'hora' => '14:00', 'minutos' => 30, 'motivo' => 'Electrocardiograma', 'estado' => 'confirmada'],
            ['paciente_id' => 6, 'doctor_id' => 3, 'dia' => 5, 'hora' => '15:00', 'minutos' => 30, 'motivo' => 'Consulta de seguimiento', 'estado' => 'cancelada'],
            ['paciente_id' => 1, 'doctor_id' => 4, 'dia' => 2, 'hora' => '16:00', 'minutos' => 30, 'motivo' => 'Revisión de lunar', 'estado' => 'pendiente'],
            ['paciente_id' => 2, 'doctor_id' => 4, 'dia' => 6, 'hora' => '10:00', 'minutos' => 30, 'motivo' => 'Tratamiento de acné', 'estado' => 'confirmada'],
        ];

        foreach ($citas as $c) {
            $inicio = $hoy->copy()->addDays($c['dia'])->setTimeFromTimeString($c['hora']);
            $fin = $inicio->copy()->addMinutes($c['minutos']);

            Cita::create([
                'paciente_id' => $c['paciente_id'],
                'doctor_id' => $c['doctor_id'],
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'motivo' => $c['motivo'],
                'estado' => $c['estado'],
            ]);
        }
    }
}
