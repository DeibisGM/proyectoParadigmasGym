<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$usuarioId = $_SESSION['usuario_id'];
$config = include '../config/gymRules.php';

include_once '../business/reservaBusiness.php';
include_once '../business/eventoBusiness.php';
include_once '../business/instructorBusiness.php';

$reservaBusiness = new ReservaBusiness();
$eventoBusiness = new EventoBusiness();

if ($tipoUsuario === 'admin') {
    $eventos = $eventoBusiness->getAllEventos();
    $instructorBusiness = new InstructorBusiness();
    $instructores = $instructorBusiness->getAllTBInstructor(true);
} else if ($tipoUsuario === 'cliente') {
    $misReservas = $reservaBusiness->getReservasPorCliente($usuarioId);
    $eventos = $eventoBusiness->getAllEventos();
} else if ($tipoUsuario === 'instructor') {
    $eventos = $eventoBusiness->getAllEventos();
    $eventosInstructor = [];
    foreach ($eventos as $evento) {
        if ($evento->getInstructorId() == $usuarioId) $eventosInstructor[] = $evento;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horarios y Reservas</title>
    <style>
        .error {
            color: red;
            border: 1px solid red;
            padding: 10px;
            margin-top: 10px;
        }

        .success {
            color: green;
            border: 1px solid green;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<header>
    <h2>Gimnasio - Horarios y Reservas</h2>
    <a href="../index.php">Volver al Inicio</a>
</header>
<hr>

<div class="container">
    <div class="main">
        <?php if ($tipoUsuario === 'cliente'): ?>
            <h3>Próximos Eventos</h3>
            <div class="proximos-eventos">
                <?php
                $eventosFuturos = array_filter($eventos, function ($evento) {
                    return $evento->getEstado() == 1 && new DateTime($evento->getFecha()) >= new DateTime(date('Y-m-d'));
                });

                if (empty($eventosFuturos)) {
                    echo "<p>No hay eventos especiales programados próximamente.</p>";
                } else {
                    foreach ($eventosFuturos as $evento) {
                        $fechaFormateada = date('d/m/Y', strtotime($evento->getFecha()));
                        echo "<div><strong>" . htmlspecialchars($evento->getNombre()) . "</strong> - " . $fechaFormateada .
                                " a las " . date('h:i A', strtotime($evento->getHoraInicio())) . "</div>";
                    }
                }
                ?>
            </div>
            <hr>
            <h3>Reservar un espacio</h3>
            <label for="fechaReserva">Selecciona una fecha para tu reserva:</label>
            <input type="date" id="fechaReserva" min="<?php echo date('Y-m-d'); ?>">
            <div id="messages"></div>
            <div id="disponibilidad-container"
                 data-duracion="<?php echo $config['USO_LIBRE_DURACION_MINUTOS']; ?>"></div>

        <?php elseif ($tipoUsuario === 'admin'): ?>
            <h3>Gestionar Eventos (Clases)</h3>
            <form action="../action/eventoAction.php" method="POST">
                <h4>Nuevo Evento</h4>
                <input type="text" name="nombre" placeholder="Nombre del Evento" required>
                <!-- CAMBIO: Input de fecha en lugar de select de día -->
                <label>Fecha del evento:</label>
                <input type="date" name="fecha" required>
                <input type="time" name="horaInicio" required>
                <input type="time" name="horaFin" required>
                <input type="number" name="aforo" placeholder="Aforo" required>
                <select name="instructorId">
                    <option value="">Sin instructor</option>
                    <?php foreach ($instructores as $instructor) echo "<option value='{$instructor->getInstructorId()}'>{$instructor->getInstructorNombre()}</option>"; ?>
                </select>
                <textarea name="descripcion" placeholder="Descripción..."></textarea>
                <button type="submit" name="create">Crear Evento</button>
            </form>

            <h4>Eventos Existentes</h4>
            <table border="1">
                <!-- CAMBIO: Encabezado de tabla -->
                <tr>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Aforo</th>
                    <th>Instructor</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($eventos as $evento): ?>
                    <tr>
                        <form action="../action/eventoAction.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $evento->getId(); ?>">
                            <td><input type="text" name="nombre"
                                       value="<?php echo htmlspecialchars($evento->getNombre()); ?>"></td>
                            <!-- CAMBIO: Input de fecha con el valor actual -->
                            <td><input type="date" name="fecha"
                                       value="<?php echo htmlspecialchars($evento->getFecha()); ?>" required></td>
                            <td><input type="time" name="horaInicio" value="<?php echo $evento->getHoraInicio(); ?>"> -
                                <input type="time" name="horaFin" value="<?php echo $evento->getHoraFin(); ?>"></td>
                            <td><input type="number" name="aforo" value="<?php echo $evento->getAforo(); ?>"></td>
                            <td>
                                <select name="instructorId">
                                    <option value="">Sin instructor</option>
                                    <?php foreach ($instructores as $instructor) echo "<option value='{$instructor->getInstructorId()}' " . ($evento->getInstructorId() == $instructor->getInstructorId() ? 'selected' : '') . ">" . htmlspecialchars($instructor->getInstructorNombre()) . "</option>"; ?>
                                </select>
                            </td>
                            <td>
                                <select name="estado">
                                    <option value="1" <?php echo $evento->getEstado() == 1 ? 'selected' : ''; ?>>
                                        Activo
                                    </option>
                                    <option value="0" <?php echo $evento->getEstado() == 0 ? 'selected' : ''; ?>>
                                        Inactivo
                                    </option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="update">Guardar</button>
                                <button type="submit" name="delete" onclick="return confirm('¿Seguro?')">Eliminar
                                </button>
                            </td>
                            <input type="hidden" name="descripcion"
                                   value="<?php echo htmlspecialchars($evento->getDescripcion()); ?>">
                        </form>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($tipoUsuario === 'instructor'): ?>
            <h3>Mis Próximas Clases</h3>
            <table border="1">
                <!-- CAMBIO: Encabezado de tabla -->
                <tr>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Aforo</th>
                </tr>
                <?php if (empty($eventosInstructor)): ?>
                    <tr>
                        <td colspan="4">No tienes clases asignadas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($eventosInstructor as $evento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($evento->getNombre()); ?></td>
                            <!-- CAMBIO: Mostrar la fecha del evento -->
                            <td><?php echo htmlspecialchars($evento->getFecha()); ?></td>
                            <td><?php echo date('h:i A', strtotime($evento->getHoraInicio())) . " - " . date('h:i A', strtotime($evento->getHoraFin())); ?></td>
                            <td><?php echo $evento->getAforo(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        <?php endif; ?>
    </div>

    <div class="sidebar">
        <?php if ($tipoUsuario === 'cliente'): ?>
            <h4>Mis Reservas</h4>
            <div id="mis-reservas-list">
                <?php if (empty($misReservas)): ?>
                    <p>No tienes reservas.</p>
                <?php else: ?>
                    <table border="1">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Horario</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                        <?php foreach ($misReservas as $reserva): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reserva->getFecha()); ?></td>
                                <td><?php echo htmlspecialchars($reserva->getEventoNombre()); ?></td>
                                <td><?php echo date('h:i A', strtotime($reserva->getHoraInicio())) . " - " . date('h:i A', strtotime($reserva->getHoraFin())); ?></td>
                                <td><?php echo htmlspecialchars($reserva->getEstado()); ?></td>
                                <td>
                                    <?php if ($reserva->getEstado() === 'activa' && new DateTime($reserva->getFecha() . ' ' . $reserva->getHoraInicio()) > new DateTime()): ?>
                                        <button onclick="cancelarReserva(<?php echo $reserva->getId(); ?>)">Cancelar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // El código JavaScript no necesita cambios, ya funciona correctamente con la nueva lógica del backend.
    let disponibilidadCache = {};
    let diaInfoCache = {};
    document.addEventListener('DOMContentLoaded', function () {
        const fechaInput = document.getElementById('fechaReserva');
        if (fechaInput) {
            fechaInput.addEventListener('change', cargarDisponibilidad);
        }
    });

    function cargarDisponibilidad() {
        const fecha = document.getElementById('fechaReserva').value;
        const container = document.getElementById('disponibilidad-container');
        if (!fecha) {
            container.innerHTML = '';
            return;
        }
        container.innerHTML = '<p>Cargando...</p>';
        fetch(`../ajax/getDisponibilidad.php?fecha=${fecha}`).then(response => response.json()).then(data => {
            container.innerHTML = '';
            if (data.success) {
                disponibilidadCache = data.data.uso_libre_slots;
                diaInfoCache = data.data.dia_info;
                let eventosHtml = '<h4>Eventos Especiales del Día</h4>';
                if (data.data.eventos.length > 0) {
                    data.data.eventos.forEach(e => {
                        eventosHtml += `<div class="slot ${e.disponibles > 0 ? 'disponible' : 'lleno'}"><strong>${e.nombre}</strong> (Instructor: ${e.instructor})<br>${e.hora_inicio.substring(0, 5)} - ${e.hora_fin.substring(0, 5)}<br><span>${e.disponibles} / ${e.aforo} disp.</span><br>${e.disponibles > 0 ? `<button onclick="reservar(${e.id}, '${fecha}', '${e.hora_inicio}')">Reservar Evento</button>` : 'Lleno'}</div>`;
                    });
                } else {
                    eventosHtml += '<p>No hay eventos programados para este día.</p>';
                }
                let usoLibreHtml = '<h4>Reserva de Uso Libre</h4>';
                const slotsDisponibles = Object.keys(disponibilidadCache);
                if (slotsDisponibles.length > 0) {
                    usoLibreHtml += `<div><label for="hora-select">Seleccione la hora de inicio:</label><br><select id="hora-select" onchange="actualizarInfoHora()"><option value="">-- Seleccionar hora --</option>`;
                    slotsDisponibles.forEach(hora => {
                        if (disponibilidadCache[hora].disponibles > 0) {
                            usoLibreHtml += `<option value="${hora}">${hora}</option>`;
                        }
                    });
                    usoLibreHtml += `</select><button onclick="reservar(null, '${fecha}')">Confirmar Reserva</button><div id="disponibilidad-info"></div></div>`;
                } else {
                    usoLibreHtml += '<p>El gimnasio está cerrado o no hay horarios disponibles para uso libre este día.</p>';
                }
                container.innerHTML = '<div class="disponibilidad-grid">' + eventosHtml + '</div><hr>' + usoLibreHtml;
            } else {
                container.innerHTML = `<p class="error">${data.message}</p>`;
            }
        }).catch(error => {
            container.innerHTML = `<p class="error">Error de conexión al cargar la disponibilidad.</p>`;
        });
    }

    function actualizarInfoHora() {
        const horaSeleccionada = document.getElementById('hora-select').value;
        const infoDiv = document.getElementById('disponibilidad-info');
        if (!horaSeleccionada || !diaInfoCache) {
            infoDiv.innerHTML = '';
            return;
        }
        const slotData = disponibilidadCache[horaSeleccionada];
        const duracionMinutos = parseInt(document.getElementById('disponibilidad-container').getAttribute('data-duracion'));
        const [horas, minutos] = horaSeleccionada.split(':');
        const fechaInicio = new Date();
        fechaInicio.setHours(horas, minutos, 0, 0);
        let fechaFinReal = new Date(fechaInicio.getTime() + duracionMinutos * 60000);
        const horaCierreDia = diaInfoCache.cierre;
        if (fechaFinReal.toTimeString().substring(0, 5) > horaCierreDia) {
            const [h, m] = horaCierreDia.split(':');
            fechaFinReal.setHours(h, m, 0, 0);
        }
        diaInfoCache.bloqueos.forEach(bloqueo => {
            const horaInicioBloqueo = bloqueo.inicio;
            if (horaSeleccionada < horaInicioBloqueo && fechaFinReal.toTimeString().substring(0, 5) > horaInicioBloqueo) {
                const [h, m] = horaInicioBloqueo.split(':');
                fechaFinReal.setHours(h, m, 0, 0);
            }
        });
        infoDiv.innerHTML = `<p><strong>Espacios disponibles:</strong> ${slotData.disponibles} <br><strong>Tu sesión terminará a las:</strong> ${fechaFinReal.toTimeString().substring(0, 5)}</p>`;
    }

    function reservar(eventoId, fecha, horaDeEvento) {
        let horaCompleta;
        if (eventoId === null) {
            horaCompleta = document.getElementById('hora-select').value;
            if (!horaCompleta) {
                alert('Por favor, seleccione una hora para la reserva de uso libre.');
                return;
            }
        } else {
            horaCompleta = horaDeEvento.substring(0, 5);
        }
        if (!confirm(`¿Confirmas esta reserva para el ${fecha} a las ${horaCompleta}?`)) return;
        const formData = new FormData();
        formData.append('action', 'create');
        formData.append('fecha', fecha);
        formData.append('hora', horaCompleta + ':00');
        if (eventoId) {
            formData.append('eventoId', eventoId);
        }
        fetch('../action/reservaAction.php', {method: 'POST', body: formData}).then(res => res.json()).then(data => {
            mostrarMensaje(data.message, data.success);
            if (data.success) {
                setTimeout(() => location.reload(), 2000);
            } else {
                cargarDisponibilidad();
            }
        }).catch(error => {
            mostrarMensaje('Error de conexión al intentar reservar.', false);
        });
    }

    function cancelarReserva(reservaId) {
        if (!confirm('¿Seguro que quieres cancelar esta reserva?')) return;
        const formData = new FormData();
        formData.append('action', 'cancel');
        formData.append('reservaId', reservaId);
        fetch('../action/reservaAction.php', {method: 'POST', body: formData}).then(res => res.json()).then(data => {
            mostrarMensaje(data.message, data.success);
            if (data.success) {
                setTimeout(() => location.reload(), 2000);
            }
        }).catch(error => {
            mostrarMensaje('Error de conexión al intentar cancelar.', false);
        });
    }

    function mostrarMensaje(mensaje, esExito) {
        const messagesDiv = document.getElementById('messages');
        messagesDiv.textContent = mensaje;
        messagesDiv.className = esExito ? 'success' : 'error';
        setTimeout(() => {
            messagesDiv.textContent = '';
            messagesDiv.className = '';
        }, 5000);
    }
</script>

</body>
</html>