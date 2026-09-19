import { Calendar } from '@fullcalendar/core';
import esLocale from '@fullcalendar/core/locales/es';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

const API_CITAS = '/api/citas';

// Las fechas de la API viajan en UTC pero representan la hora local del
// hospital tal cual se guardo; se formatean sin convertir de zona horaria
// para que "09:00" siempre se muestre como "09:00", sin importar en que
// zona horaria este el navegador que las visualiza.
function formatearHorario(fechaInicio, fechaFin) {
    const opciones = { dateStyle: 'medium', timeStyle: 'short', timeZone: 'UTC' };
    const inicio = new Date(fechaInicio).toLocaleString('es-GT', opciones);
    const fin = new Date(fechaFin).toLocaleTimeString('es-GT', { timeStyle: 'short', timeZone: 'UTC' });

    return `${inicio} - ${fin}`;
}

async function obtenerCitas(info, exito, fracaso) {
    try {
        const parametros = new URLSearchParams({
            desde: info.startStr,
            hasta: info.endStr,
        });

        const respuesta = await fetch(`${API_CITAS}?${parametros}`, {
            headers: { Accept: 'application/json' },
        });

        if (!respuesta.ok) {
            throw new Error('No se pudieron cargar las citas.');
        }

        const { data } = await respuesta.json();

        exito(
            data.map((cita) => ({
                id: cita.id,
                title: `${cita.paciente.nombre_completo} · ${cita.doctor.nombre_completo}`,
                start: cita.fecha_inicio,
                end: cita.fecha_fin,
                backgroundColor: cita.color,
                borderColor: cita.color,
                extendedProps: cita,
            }))
        );
    } catch (error) {
        fracaso(error);
    }
}

function mostrarDetalle(cita) {
    document.getElementById('detalle-paciente').textContent = cita.paciente.nombre_completo;
    document.getElementById('detalle-doctor').textContent = `${cita.doctor.nombre_completo} (${cita.doctor.especialidad})`;
    document.getElementById('detalle-horario').textContent = formatearHorario(cita.fecha_inicio, cita.fecha_fin);
    document.getElementById('detalle-motivo').textContent = cita.motivo;
    document.getElementById('detalle-estado').textContent = cita.estado_label;

    document.getElementById('modal-detalle').classList.remove('hidden');
    document.getElementById('modal-detalle').classList.add('flex');
}

function cerrarDetalle() {
    document.getElementById('modal-detalle').classList.add('hidden');
    document.getElementById('modal-detalle').classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('calendario');

    const calendario = new Calendar(contenedor, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
        locale: esLocale,
        timeZone: 'UTC',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek',
        },
        height: 'auto',
        events: obtenerCitas,
        eventClick: (info) => mostrarDetalle(info.event.extendedProps),
    });

    calendario.render();

    document.getElementById('cerrar-modal-detalle').addEventListener('click', cerrarDetalle);
    document.getElementById('modal-detalle').addEventListener('click', (evento) => {
        if (evento.target.id === 'modal-detalle') {
            cerrarDetalle();
        }
    });
});
