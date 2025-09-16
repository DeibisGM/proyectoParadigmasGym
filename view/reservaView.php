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

$reservaBusiness = new ReservaBusiness();
$eventoBusiness = new EventoBusiness();

// Carga de datos según el tipo de usuario
if ($tipoUsuario === 'admin' || $tipoUsuario === 'instructor') {
    $todasLasReservas = $reservaBusiness->getAllReservas(); // Se implementará este método
} else { // Cliente
    $misReservas = $reservaBusiness->getReservasPorCliente($usuarioId);
    $eventos = $eventoBusiness->getAllEventos();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horarios y Reservas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-calendar-check"></i>
            <?php
            if ($tipoUsuario === 'cliente') {
                echo "Horarios y Reservas";
            } else {
                echo "Vista General de Reservas";
            }
            ?>
        </h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

    <main>
        <?php if ($tipoUsuario === 'cliente'): ?>
            <!-- VISTA DEL CLIENTE -->
            <section>
                <h3><i class="ph ph-sparkle"></i>Próximos Eventos</h3>
                <?php
                $eventosFuturos = array_filter($eventos, function ($e) {
                    return $e->getEstado() == 1 && new DateTime($e->getFecha()) >= new DateTime(date('Y-m-d'));
                });
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
                <div id="disponibilidad-container" data-duracion="<?php echo $config['USO_LIBRE_DURACION_MINUTOS']; ?>"></div>
            </section>

            <section>
                <h3><i class="ph ph-list-checks"></i>Mis Reservas</h3>
                <div id="mis-reservas-list">
                    <?php if (empty($misReservas)): ?>
                        <p>No tienes reservas activas.</p>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
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
                                                <button onclick="cancelarReserva(<?php echo $reserva->getId(); ?>)"><i class="ph ph-x-circle"></i> Cancelar</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        <?php elseif (in_array($tipoUsuario, ['admin', 'instructor'])): ?>
            <!-- VISTA DE ADMIN/INSTRUCTOR -->
            <section>
                <h3><i class="ph ph-users-three"></i>Todas las Reservas</h3>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Cliente</th>
                            <th>Tipo de Reserva</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($todasLasReservas)): ?>
                            <tr><td colspan="5">No hay ninguna reserva registrada.</td></tr>
                        <?php else: ?>
                            <?php foreach ($todasLasReservas as $reserva): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reserva->getFecha()) ?></td>
                                    <td><?= date('h:i A', strtotime($reserva->getHoraInicio())) . " - " . date('h:i A', strtotime($reserva->getHoraFin())) ?></td>
                                    <td><?= htmlspecialchars($reserva->getClienteNombre()) ?></td>
                                    <td><?= htmlspecialchars($reserva->getEventoNombre()) ?></td>
                                    <td><?= htmlspecialchars($reserva->getEstado()) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
        fetch(`../ajax/getDisponibilidad.php?fecha=${fecha}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const {eventos, uso_libre_slots} = data.data;
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
            }).catch(error => container.innerHTML = `<p class="error">Error al cargar la disponibilidad.</p>`);
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
