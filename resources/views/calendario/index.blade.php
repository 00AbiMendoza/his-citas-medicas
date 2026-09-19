<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/calendario.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="mx-auto max-w-7xl px-4 py-6">
        <header class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">HIS · Control de Citas Médicas</h1>
                <p class="text-sm text-slate-500">Calendario interactivo de citas del Sistema Hospitalario Integrado</p>
            </div>

            <ul class="flex flex-wrap gap-3 text-xs">
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#f59e0b"></span> Pendiente</li>
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#3b82f6"></span> Confirmada</li>
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#10b981"></span> Atendida</li>
                <li class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background:#ef4444"></span> Cancelada</li>
            </ul>
        </header>

        <div class="mb-4 flex items-center gap-2">
            <label for="filtro-doctor" class="text-sm font-medium text-slate-600">Doctor</label>
            <select id="filtro-doctor" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500">
                <option value="">Todos los doctores</option>
            </select>
        </div>

        <p class="mb-3 text-xs text-slate-500">
            Haz clic y arrastra sobre el calendario para crear una cita. Arrastra un evento existente para reprogramarlo.
        </p>

        <div class="rounded-xl bg-white p-3 shadow sm:p-5">
            <div id="calendario"></div>
        </div>
    </div>

    {{-- Modal de detalle de cita --}}
    <div id="modal-detalle" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-start justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Detalle de la cita</h2>
                <button type="button" id="cerrar-modal-detalle" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Paciente</dt>
                    <dd id="detalle-paciente" class="text-right font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Doctor</dt>
                    <dd id="detalle-doctor" class="text-right font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Horario</dt>
                    <dd id="detalle-horario" class="text-right font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Motivo</dt>
                    <dd id="detalle-motivo" class="text-right font-medium"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Estado</dt>
                    <dd id="detalle-estado" class="text-right font-medium"></dd>
                </div>
            </dl>

            <p id="detalle-error" class="mt-3 hidden rounded-lg bg-red-50 px-3 py-2 text-xs text-red-600"></p>

            <div class="mt-5 flex flex-wrap gap-2">
                <button type="button" data-estado="confirmada" class="btn-estado rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">Confirmar</button>
                <button type="button" data-estado="atendida" class="btn-estado rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">Marcar atendida</button>
                <button type="button" data-estado="cancelada" class="btn-estado rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">Cancelar cita</button>
            </div>
        </div>
    </div>

    {{-- Modal de creacion de cita --}}
    <div id="modal-crear" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-start justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Nueva cita</h2>
                <button type="button" id="cerrar-modal-crear" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form id="form-crear-cita" class="space-y-3 text-sm">
                <div>
                    <label for="crear-paciente" class="mb-1 block font-medium text-slate-600">Paciente</label>
                    <select id="crear-paciente" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"></select>
                </div>
                <div>
                    <label for="crear-doctor" class="mb-1 block font-medium text-slate-600">Doctor</label>
                    <select id="crear-doctor" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"></select>
                </div>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label for="crear-inicio" class="mb-1 block font-medium text-slate-600">Inicio</label>
                        <input type="datetime-local" id="crear-inicio" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    </div>
                    <div class="flex-1">
                        <label for="crear-fin" class="mb-1 block font-medium text-slate-600">Fin</label>
                        <input type="datetime-local" id="crear-fin" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    </div>
                </div>
                <div>
                    <label for="crear-motivo" class="mb-1 block font-medium text-slate-600">Motivo</label>
                    <textarea id="crear-motivo" required rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"></textarea>
                </div>

                <p id="crear-error" class="hidden rounded-lg bg-red-50 px-3 py-2 text-xs text-red-600"></p>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="cancelar-crear" class="rounded-lg px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-100">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-1.5 text-sm font-medium text-white hover:bg-slate-700">Guardar cita</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
