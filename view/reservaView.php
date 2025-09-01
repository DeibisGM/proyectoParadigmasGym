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
    $eventosInstructor = array_filter($eventos, fn($evento) => $evento->getInstructorId() == $usuarioId);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horarios y Reservas</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 4px;
        }

        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 4px;
        }

        .page-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 900px) {
            .page-layout {
                grid-template-columns: 1fr;
            }
        }

        .disponibilidad-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .slot {
            border: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: 4px;
        }

        .slot.lleno {
            background-color: #f8f9fa;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-calendar-check"></i>Horarios y Reservas</h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

    <main>
        <?php if ($tipoUsuario === 'cliente'): ?>
            <div class="page-layout">
                <div>
                    <section>
                        <h3><i class="ph ph-sparkle"></i>Próximos Eventos</h3>
                        <?php
                        $eventosFuturos = array_filter($eventos, fn($e) => $e->getEstado() == 1 && new DateTime($e->getFecha()) >= new DateTime(date('Y-m-d')));
                        if (empty($eventosFuturos)) {
                            echo "<p>No hay eventos especiales programados próximamente.</p>";
                        } else {
                            foreach ($eventosFuturos as $evento) {
                                echo "<div><strong>" . htmlspecialchars($evento->getNombre()) . "</strong> - " . date('d/m/Y', strtotime($evento->getFecha())) . " a las " . date('h:i A', strtotime($evento->getHoraInicio())) . "</div>";
                            }
                        }
                        ?>
                    </section>
                    <section>
                        <h3><i class="ph ph-plus-circle"></i>Reservar un espacio</h3>
                        <label for="fechaReserva">Selecciona una fecha para tu reserva:</label>
                        <input type="date" id="fechaReserva" min="<?php echo date('Y-m-d'); ?>">
                        <div id="messages"></div>
                        <div id="disponibilidad-container"
                             data-duracion="<?php echo $config['USO_LIBRE_DURACION_MINUTOS']; ?>"></div>
                    </section>
                </div>
                <aside>
                    <section>
                        <h4><i class="ph ph-list-checks"></i>Mis Reservas</h4>
                        <div id="mis-reservas-list">
                            <?php if (empty($misReservas)): ?>
                                <p>No tienes reservas.</p>
                            <?php else: ?>
                                <table>
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Horario</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($misReservas as $reserva): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva->getFecha()); ?></td>
                                            <td><?php echo htmlspecialchars($reserva->getEventoNombre()); ?></td>
                                            <td><?php echo date('h:i A', strtotime($reserva->getHoraInicio())) . " - " . date('h:i A', strtotime($reserva->getHoraFin())); ?></td>
                                            <td><?php echo htmlspecialchars($reserva->getEstado()); ?></td>
                                            <td>
                                                <?php if ($reserva->getEstado() === 'activa' && new DateTime($reserva->getFecha() . ' ' . $reserva->getHoraInicio()) > new DateTime()): ?>
                                                    <button onclick="cancelarReserva(<?php echo $reserva->getId(); ?>)">
                                                        <i class="ph ph-x-circle"></i> Cancel</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </section>
                </aside>
            </div>

        <?php elseif ($tipoUsuario === 'admin'): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Gestionar Eventos</h3>
                <form action="../action/eventoAction.php" method="POST">
                    <h4>Nuevo Evento</h4>
                    <input type="text" name="nombre" placeholder="Nombre del Evento" required>
                    <label>Fecha del evento:</label>
                    <input type="date" name="fecha" required>
                    <input type="time" name="hora_inicio" required>
                    <input type="time" name="hora_fin" required>
                    <input type="number" name="aforo" placeholder="Aforo" required>
                    <select name="instructor_id">
                        <option value="">Sin instructor</option>
                        <?php foreach ($instructores as $instructor) echo "<option value='{$instructor->getInstructorId()}'>" . htmlspecialchars($instructor->getInstructorNombre()) . "</option>"; ?>
                    </select>
                    <textarea name="descripcion" placeholder="Descripción..."></textarea>
                    <button type="submit" name="crear_evento"><i class="ph ph-plus"></i>Crear Evento</button>
                </form>

                <h4>Eventos Existentes</h4>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Aforo</th>
                            <th>Instructor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($eventos as $evento): ?>
                            <tr>
                                <form action="../action/eventoAction.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $evento->getId(); ?>">
                                    <td><input type="text" name="nombre" value="<?php echo htmlspecialchars($evento->getNombre()); ?>" placeholder="Nombre del Evento"></td>
                                    <td><input type="date" name="fecha" value="<?php echo htmlspecialchars($evento->getFecha()); ?>" required>
                                    </td>
                                    <td><input type="time" name="horaInicio" value="<?php echo $evento->getHoraInicio(); ?>"> - <input type="time" name="horaFin" value="<?php echo $evento->getHoraFin(); ?>">
                                    </td>
                                    <td><input type="number" name="aforo" value="<?php echo $evento->getAforo(); ?>" placeholder="Aforo">
                                    </td>
                                    <td>
                                        <select name="instructorId">
                                            <option value="">Sin instructor</option>
                                            <?php foreach ($instructores as $i) echo "<option value='{$i->getInstructorId()}' " . ($evento->getInstructorId() == $i->getInstructorId() ? 'selected' : '') . ">" . htmlspecialchars($i->getInstructorNombre()) . "</option>"; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="estado">
                                            <option value="1" <?= $evento->getEstado() == 1 ? 'selected' : ''; ?>>
                                                Activo
                                            </option>
                                            <option value="0" <?= $evento->getEstado() == 0 ? 'selected' : ''; ?>>
                                                Inactivo
                                            </option>
                                        </select>
                                    </td>
                                    <td class="actions-cell">
                                        <button type="submit" name="update" title="Guardar"><i
                                                    class="ph ph-floppy-disk"></i> Guardar</button>
                                        <button type="submit" name="eliminar_evento"
                                                onclick="return confirm('¿Seguro?')" title="Eliminar"><i
                                                    class="ph ph-trash"></i> Eliminar</button>
                                    </td>
                                    <input type="hidden" name="descripcion"
                                           value="<?= htmlspecialchars($evento->getDescripcion()); ?>">
                                </form>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php elseif ($tipoUsuario === 'instructor'): ?>
            <section>
                <h3><i class="ph ph-chalkboard-teacher"></i>Mis Próximas Clases</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Aforo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($eventosInstructor)): ?>
                        <tr>
                            <td colspan="4">No tienes clases asignadas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eventosInstructor as $evento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($evento->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($evento->getFecha()); ?></td>
                                <td><?php echo date('h:i A', strtotime($evento->getHoraInicio())) . " - " . date('h:i A', strtotime($evento->getHoraFin())); ?></td>
                                <td><?php echo $evento->getAforo(); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('fechaReserva')?.addEventListener('change', cargarDisponibilidad);
    });

    function cargarDisponibilidad() {
        const fecha = document.getElementById('fechaReserva').value;
        const container = document.getElementById('disponibilidad-container');
        if (!fecha) {
            container.innerHTML = '';
            return;
        }
        container.innerHTML = '<p>Cargando...</p>';
        fetch(`../action/getDisponibilidad.php?fecha=${fecha}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const {eventos, uso_libre_slots, dia_info} = data.data;
                    let html = '<h4><i class="ph ph-sparkle"></i>Eventos Especiales</h4>';
                    if (eventos.length > 0) {
                        html += '<div class="disponibilidad-grid">';
                        eventos.forEach(e => {
                            html += `<div class="slot ${e.disponibles > 0 ? '' : 'lleno'}"><strong>${e.nombre}</strong><br><small>Instructor: ${e.instructor}</small><br>${e.hora_inicio.substring(0, 5)} - ${e.hora_fin.substring(0, 5)}<br><span>${e.disponibles}/${e.aforo} disp.</span><br>${e.disponibles > 0 ? `<button onclick="reservar(${e.id}, '${fecha}', '${e.hora_inicio}')">Reservar</button>` : 'Lleno'}</div>`;
                        });
                        html += '</div>';
                    } else {
                        html += '<p>No hay eventos programados para este día.</p>';
                    }
                    html += '<hr><h4><i class="ph ph-barbell"></i>Reserva de Uso Libre</h4>';
                    const slotsDisponibles = Object.keys(uso_libre_slots).filter(h => uso_libre_slots[h].disponibles > 0);
                    if (slotsDisponibles.length > 0) {
                        html += `<div><label for="hora-select">Seleccione la hora de inicio:</label><br><select id="hora-select"><option value="">-- Hora --</option>`;
                        slotsDisponibles.forEach(hora => {
                            html += `<option value="${hora}">${hora}</option>`;
                        });
                        html += `</select><button onclick="reservar(null, '${fecha}')">Confirmar</button></div>`;
                    } else {
                        html += '<p>No hay horarios disponibles para uso libre este día.</p>';
                    }
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `<p class="error">${data.message}</p>`;
                }
            }).catch(error => container.innerHTML = `<p class="error">Error de conexión.</p>`);
    }

    function reservar(eventoId, fecha, horaDeEvento) {
        let horaCompleta = eventoId ? horaDeEvento.substring(0, 5) : document.getElementById('hora-select').value;
        if (!horaCompleta) {
            alert('Seleccione una hora.');
            return;
        }
        if (!confirm(`¿Confirmas esta reserva para el ${fecha} a las ${horaCompleta}?`)) return;

        const formData = new FormData();
        formData.append('action', 'create');
        formData.append('fecha', fecha);
        formData.append('hora', horaCompleta + ':00');
        if (eventoId) formData.append('eventoId', eventoId);

        fetch('../action/reservaAction.php', {method: 'POST', body: formData})
            .then(res => res.json())
            .then(data => {
                mostrarMensaje(data.message, data.success);
                if (data.success) setTimeout(() => location.reload(), 2000);
            }).catch(error => mostrarMensaje('Error de conexión al reservar.', false));
    }

    function cancelarReserva(reservaId) {
        if (!confirm('¿Cancelar esta reserva?')) return;
        const formData = new FormData();
        formData.append('action', 'cancel');
        formData.append('reservaId', reservaId);
        fetch('../action/reservaAction.php', {method: 'POST', body: formData})
            .then(res => res.json())
            .then(data => {
                mostrarMensaje(data.message, data.success);
                if (data.success) setTimeout(() => location.reload(), 2000);
            }).catch(error => mostrarMensaje('Error de conexión al cancelar.', false));
    }

    function mostrarMensaje(mensaje, esExito) {
        const div = document.getElementById('messages');
        div.textContent = mensaje;
        div.className = esExito ? 'success' : 'error';
        setTimeout(() => {
            div.textContent = '';
            div.className = '';
        }, 5000);
    }
</script>
</body>
</html>