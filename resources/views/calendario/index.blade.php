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
        </div>
    </div>
</body>
</html>
