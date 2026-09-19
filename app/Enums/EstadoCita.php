<?php

namespace App\Enums;

enum EstadoCita: string
{
    case Pendiente = 'pendiente';
    case Confirmada = 'confirmada';
    case Cancelada = 'cancelada';
    case Atendida = 'atendida';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Confirmada => 'Confirmada',
            self::Cancelada => 'Cancelada',
            self::Atendida => 'Atendida',
        };
    }

    /**
     * Color hexadecimal usado por FullCalendar para representar el estado (RQF-10).
     */
    public function color(): string
    {
        return match ($this) {
            self::Pendiente => '#f59e0b',
            self::Confirmada => '#3b82f6',
            self::Atendida => '#10b981',
            self::Cancelada => '#ef4444',
        };
    }

    /**
     * Estados a los que se puede transicionar desde el estado actual.
     * Cancelada y Atendida son estados finales.
     *
     * @return array<self>
     */
    public function transicionesPermitidas(): array
    {
        return match ($this) {
            self::Pendiente => [self::Confirmada, self::Cancelada],
            self::Confirmada => [self::Atendida, self::Cancelada],
            self::Cancelada, self::Atendida => [],
        };
    }

    public function puedeTransicionarA(self $nuevoEstado): bool
    {
        return $this === $nuevoEstado || in_array($nuevoEstado, $this->transicionesPermitidas(), true);
    }
}
