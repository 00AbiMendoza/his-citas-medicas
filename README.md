# Hospital Nuevo Horizonte · Módulo de Control de Citas Médicas (HIS)

Módulo funcional del Sistema Hospitalario Integrado (HIS) del **Hospital Nuevo Horizonte**:
un calendario interactivo de citas médicas construido con Laravel 12, MySQL en Docker y
FullCalendar. Desarrollado para la Serie II del curso de **Análisis de Sistemas II**.

La evidencia completa (flujo Git, comandos, respuestas de la API, pruebas manuales) está en
[`EVIDENCIA.md`](EVIDENCIA.md).

## Stack

- **Backend:** Laravel 12 (PHP 8.4), arquitectura por capas (Controller → Service → Model).
- **Base de datos:** MySQL 8.0, exclusivamente en Docker con volumen persistente.
- **Frontend:** Blade + FullCalendar 6 (vistas mes/semana/agenda, drag & drop) vía Vite.
- **Control de versiones:** flujo de ramas por feature, Pull Request y merge documentado.

## Funcionalidad

- Crear una cita indicando paciente, doctor, fecha/hora de inicio-fin y motivo.
- Ver las citas en un calendario interactivo (vistas de mes y semana), coloreadas según su
  estado: pendiente, confirmada, atendida o cancelada.
- Reprogramar una cita arrastrándola en el calendario; el cambio se valida y persiste vía API.
- Cambiar el estado de una cita (confirmar / marcar atendida / cancelar) sin borrar el
  registro histórico.
- Filtrar las citas por doctor.
- Validación de doble reserva **en el servidor**: no puede existir más de una cita activa
  para el mismo doctor en horarios que se solapan.

## Arquitectura por capas

```
resources/views, resources/js   → Presentación (calendario, modales)
app/Http/Controllers/Api        → API (controladores delgados, sin lógica de negocio)
app/Http/Requests               → Validación de entrada (respuestas 400)
app/Services                    → Lógica de negocio (conflictos de horario, estados)
app/Models                      → Acceso a datos (Eloquent)
app/Enums/EstadoCita            → Máquina de estados y colores del calendario
app/Exceptions                  → Errores de negocio mapeados a códigos HTTP (409)
```

## Poner el proyecto en marcha

```bash
git clone https://github.com/00AbiMendoza/his-citas-medicas.git
cd his-citas-medicas
cp .env.example .env
composer install
php artisan key:generate

docker compose up -d          # MySQL con persistencia — un solo comando
php artisan migrate --seed    # esquema + datos semilla (pacientes, doctores, citas)

npm install
npm run build                 # o "npm run dev" para desarrollo con recarga en caliente
php artisan serve
```

Abrir `http://127.0.0.1:8000/`.

## API

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/citas` | Lista citas; filtros `doctor_id`, `paciente_id`, `desde`, `hasta` |
| POST | `/api/citas` | Crea una cita (`201`; `409` si hay conflicto de horario) |
| GET | `/api/citas/{id}` | Detalle de una cita |
| PUT | `/api/citas/{id}` | Reprograma fecha/hora (usado por drag & drop) |
| PATCH | `/api/citas/{id}/estado` | Cambia el estado (`409` si la transición no es válida) |
| GET | `/api/doctores` | Lista de doctores |
| GET | `/api/pacientes` | Lista de pacientes |

Respuestas en JSON. Códigos de error: `400` datos inválidos, `404` no encontrado, `409`
conflicto de horario o de estado.

## Backlog y trazabilidad

El desarrollo siguió el backlog de requisitos funcionales (RQF-01 a RQF-10) y no funcionales
(RQNF-01 a RQNF-08) del curso, implementado en ramas de feature independientes, cada una
fusionada a `main` mediante Pull Request. El detalle rama por rama, con los RQF/RQNF que
cubre cada una, está documentado en `EVIDENCIA.md`.
