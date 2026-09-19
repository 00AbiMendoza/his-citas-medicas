import { Calendar } from '@fullcalendar/core';
import esLocale from '@fullcalendar/core/locales/es';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

const API_CITAS = '/api/citas';
const API_DOCTORES = '/api/doctores';
const API_PACIENTES = '/api/pacientes';

let calendario;
let citaSeleccionada = null;

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

// Convierte un Date a "YYYY-MM-DDTHH:mm" usando sus componentes UTC, para
// precargar un <input type="datetime-local"> sin que el navegador aplique
// su propio desfase de zona horaria.
function aInputDatetimeLocal(fecha) {
    const pad = (n) => String(n).padStart(2, '0');

    return `${fecha.getUTCFullYear()}-${pad(fecha.getUTCMonth() + 1)}-${pad(fecha.getUTCDate())}T${pad(fecha.getUTCHours())}:${pad(fecha.getUTCMinutes())}`;
}

async function peticionJson(url, opciones = {}) {
    const respuesta = await fetch(url, {
        ...opciones,
        headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...opciones.headers },
    });

    const cuerpo = await respuesta.json().catch(() => ({}));

    if (!respuesta.ok) {
        const error = new Error(cuerpo.message || 'Ocurrio un error inesperado.');
        error.errores = cuerpo.errors;
        throw error;
    }

    return cuerpo.data;
}

async function cargarSelect(elementoId, url, etiqueta) {
    const select = document.getElementById(elementoId);
    const registros = await peticionJson(url);

    registros.forEach((registro) => {
        const opcion = document.createElement('option');
        opcion.value = registro.id;
        opcion.textContent = etiqueta(registro);
        select.appendChild(opcion);
    });
}

async function cargarFiltroDoctores() {
    const registros = await peticionJson(API_DOCTORES);
    const select = document.getElementById('filtro-doctor');

    registros.forEach((doctor) => {
        const opcion = document.createElement('option');
        opcion.value = doctor.id;
        opcion.textContent = doctor.nombre_completo;
        select.appendChild(opcion);
    });
}

async function obtenerCitas(info, exito, fracaso) {
    try {
        const doctorId = document.getElementById('filtro-doctor').value;
        const parametros = new URLSearchParams({ desde: info.startStr, hasta: info.endStr });

        if (doctorId) {
            parametros.set('doctor_id', doctorId);
        }

        const citas = await peticionJson(`${API_CITAS}?${parametros}`);

        exito(
            citas.map((cita) => ({
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

function mostrarModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}

function ocultarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}

function mostrarDetalle(cita) {
    citaSeleccionada = cita;

    document.getElementById('detalle-paciente').textContent = cita.paciente.nombre_completo;
    document.getElementById('detalle-doctor').textContent = `${cita.doctor.nombre_completo} (${cita.doctor.especialidad})`;
    document.getElementById('detalle-horario').textContent = formatearHorario(cita.fecha_inicio, cita.fecha_fin);
    document.getElementById('detalle-motivo').textContent = cita.motivo;
    document.getElementById('detalle-estado').textContent = cita.estado_label;
    document.getElementById('detalle-error').classList.add('hidden');

    mostrarModal('modal-detalle');
}

async function cambiarEstado(nuevoEstado) {
    const errorEl = document.getElementById('detalle-error');
    errorEl.classList.add('hidden');

    try {
        await peticionJson(`${API_CITAS}/${citaSeleccionada.id}/estado`, {
            method: 'PATCH',
            body: JSON.stringify({ estado: nuevoEstado }),
        });

        ocultarModal('modal-detalle');
        calendario.refetchEvents();
    } catch (error) {
        errorEl.textContent = error.message;
        errorEl.classList.remove('hidden');
    }
}

function abrirCreacion(seleccion) {
    let inicio = seleccion.start;
    let fin = seleccion.end;

    // Un clic sobre un dia completo (vista de mes) no trae una hora util;
    // se propone un horario razonable por defecto en vez de un bloque de 24h.
    if (seleccion.allDay) {
        inicio = new Date(Date.UTC(inicio.getUTCFullYear(), inicio.getUTCMonth(), inicio.getUTCDate(), 9, 0));
        fin = new Date(inicio.getTime() + 30 * 60000);
    } else if (!fin || fin <= inicio) {
        fin = new Date(inicio.getTime() + 30 * 60000);
    }

    document.getElementById('crear-inicio').value = aInputDatetimeLocal(inicio);
    document.getElementById('crear-fin').value = aInputDatetimeLocal(fin);
    document.getElementById('crear-motivo').value = '';
    document.getElementById('crear-error').classList.add('hidden');

    mostrarModal('modal-crear');
}

async function guardarCita(evento) {
    evento.preventDefault();

    const errorEl = document.getElementById('crear-error');
    errorEl.classList.add('hidden');

    const datos = {
        paciente_id: Number(document.getElementById('crear-paciente').value),
        doctor_id: Number(document.getElementById('crear-doctor').value),
        fecha_inicio: document.getElementById('crear-inicio').value.replace('T', ' '),
        fecha_fin: document.getElementById('crear-fin').value.replace('T', ' '),
        motivo: document.getElementById('crear-motivo').value,
    };

    try {
        await peticionJson(API_CITAS, { method: 'POST', body: JSON.stringify(datos) });

        ocultarModal('modal-crear');
        calendario.refetchEvents();
    } catch (error) {
        const detalle = error.errores ? Object.values(error.errores).flat().join(' ') : error.message;
        errorEl.textContent = detalle;
        errorEl.classList.remove('hidden');
    }
}

async function reprogramarEvento(info) {
    try {
        await peticionJson(`${API_CITAS}/${info.event.id}`, {
            method: 'PUT',
            body: JSON.stringify({
                fecha_inicio: info.event.startStr.slice(0, 19).replace('T', ' '),
                fecha_fin: info.event.endStr.slice(0, 19).replace('T', ' '),
            }),
        });

        calendario.refetchEvents();
    } catch (error) {
        window.alert(`No se pudo reprogramar la cita: ${error.message}`);
        info.revert();
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const contenedor = document.getElementById('calendario');

    calendario = new Calendar(contenedor, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        locale: esLocale,
        timeZone: 'UTC',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek',
        },
        height: 'auto',
        selectable: true,
        editable: true,
        eventDurationEditable: false,
        events: obtenerCitas,
        select: abrirCreacion,
        eventClick: (info) => mostrarDetalle(info.event.extendedProps),
        eventDrop: reprogramarEvento,
    });

    calendario.render();

    await Promise.all([
        cargarFiltroDoctores(),
        cargarSelect('crear-paciente', API_PACIENTES, (p) => p.nombre_completo),
        cargarSelect('crear-doctor', API_DOCTORES, (d) => `${d.nombre_completo} · ${d.especialidad}`),
    ]);

    document.getElementById('filtro-doctor').addEventListener('change', () => calendario.refetchEvents());

    document.getElementById('cerrar-modal-detalle').addEventListener('click', () => ocultarModal('modal-detalle'));
    document.getElementById('cerrar-modal-crear').addEventListener('click', () => ocultarModal('modal-crear'));
    document.getElementById('cancelar-crear').addEventListener('click', () => ocultarModal('modal-crear'));

    document.querySelectorAll('.btn-estado').forEach((boton) => {
        boton.addEventListener('click', () => cambiarEstado(boton.dataset.estado));
    });

    document.getElementById('form-crear-cita').addEventListener('submit', guardarCita);

    ['modal-detalle', 'modal-crear'].forEach((id) => {
        document.getElementById(id).addEventListener('click', (evento) => {
            if (evento.target.id === id) {
                ocultarModal(id);
            }
        });
    });
});
