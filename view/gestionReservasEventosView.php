<?php

session_start();

include_once '../business/reservaBusiness.php';
include_once '../business/eventoBusiness.php';
include_once '../business/horarioBusiness.php';
include_once '../business/instructorBusiness.php';
include_once '../business/clienteBusiness.php';
echo "DEBUG: After some includes";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: loginView.php");
    exit();
}

// Obtener datos de la sesión
$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
echo "DEBUG: User type: $tipoUsuario";

// Instanciar todos los business layers
$reservaBusiness = new ReservaBusiness();
$eventoBusiness = new EventoBusiness();
$horarioBusiness = new HorarioBusiness();
$instructorBusiness = new InstructorBusiness();
$clienteBusiness = new ClienteBusiness();

// Cargar datos comunes o específicos de la vista
$reglasGimnasio = include '../config/gymRules.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Eventos y Reservas</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .container {
            padding: 20px;
        }

        .section {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
        }

        h2, h3 {
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        #calendario {
            max-width: 400px;
        }

        .dia-no-disponible {
            background-color: #f8d7da;
        }

        #slots-disponibles button {
            margin: 5px;
        }
    </style>
</head>
<body>

<header>
    <h1>Gimnasio - Eventos y Reservas</h1>
    <a href="../index.php">Volver al Menú Principal</a>
</header>
<hr>

<div class="container">

    <?php
    echo "DEBUG: Entering role-based view section";
    // ====================================================================
    // VISTA DE ADMINISTRADOR
    // ====================================================================
    if ($tipoUsuario == 'admin'):
        echo "DEBUG: Admin view started";

        echo "<br>DEBUG: Getting all events...";
        $todosLosEventos = $eventoBusiness->getAllEventos();
        echo "<br>DEBUG: Got all events.";

        echo "<br>DEBUG: Getting all instructors...";
        $todosLosInstructores = $instructorBusiness->getAllTBInstructor(true);
        echo "<br>DEBUG: Got all instructors.";

        echo "<br>DEBUG: Getting all reservations...";
        $todasLasReservas = $reservaBusiness->getAllReservas();
        echo "<br>DEBUG: Got all reservations.";

        echo "<br>DEBUG: Getting all clients...";
        $todosLosClientes = $clienteBusiness->getAllTBCliente();
        echo "<br>DEBUG: Got all clients.";

        ?>
        <div class="section">
            <h3>Crear Nuevo Evento</h3>
            <form action="../action/eventoAction.php" method="post">
                <p><label>Nombre: <input type="text" name="nombre" required></label></p>
                <p><label>Descripción: <textarea name="descripcion"></textarea></label></p>
                <p><label>Fecha: <input type="date" name="fecha" required></label></p>
                <p>
                    <label>Hora Inicio: <input type="time" name="hora_inicio" required></label>
                    <label>Hora Fin: <input type="time" name="hora_fin" required></label>
                </p>
                <p><label>Aforo (capacidad): <input type="number" name="aforo" value="10" required></label></p>
                <p>
                    <label>Asignar Instructor:
                        <select name="instructor_id" required>
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($todosLosInstructores as $instructor): ?>
                                <option value="<?= $instructor->getInstructorId() ?>"><?= htmlspecialchars($instructor->getInstructorNombre()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </p>
                <button type="submit" name="crear_evento">Crear Evento</button>
            </form>
        </div>

        <div class="section">
            <h3>Todos los Eventos del Sistema</h3>
            <table>
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Instructor</th>
                    <th>Aforo</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($todosLosEventos as $evento): ?>
                    <tr>
                        <td><?= htmlspecialchars($evento->getNombre()) ?></td>
                        <td><?= $evento->getFecha() ?></td>
                        <td><?= $evento->getHoraInicio() ?> - <?= $evento->getHoraFin() ?></td>
                        <td>
                            <?php
                            foreach ($todosLosInstructores as $instructor) {
                                if ($instructor->getInstructorId() == $evento->getInstructorId()) {
                                    echo htmlspecialchars($instructor->getInstructorNombre());
                                    break;
                                }
                            }
                            ?>
                        </td>
                        <td><?= $evento->getAforo() ?></td>
                        <td>
                            <form action="../action/eventoAction.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $evento->getId() ?>">
                                <button type="submit" name="eliminar_evento" onclick="return confirm('¿Está seguro?');">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>Todas las Reservas del Sistema</h3>
            <table>
                <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Fecha y Hora</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php
                echo "DEBUG: Entering reservations loop";
                foreach ($todasLasReservas as $reserva):
                    $nombreCliente = 'Desconocido';
                    foreach ($todosLosClientes as $cliente) {
                        if ($cliente->getId() == $reserva->getClienteId()) {
                            $nombreCliente = $cliente->getNombre();
                            break;
                        }
                    }
                    $tipoReserva = $reserva->getEventoId() ? 'Evento' : 'Uso Libre';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($nombreCliente) ?></td>
                        <td><?= $tipoReserva ?></td>
                        <td><?= $reserva->getFecha() ?> @ <?= $reserva->getHoraInicio() ?></td>
                        <td><?= htmlspecialchars($reserva->getEstado()) ?></td>
                    </tr>
                <?php endforeach;
                echo "DEBUG: Exited reservations loop";
                ?>
                </tbody>
            </table>
        </div>

    <?php
    // ====================================================================
    // VISTA DE INSTRUCTOR
    // ====================================================================
    elseif ($tipoUsuario == 'instructor'):
    echo "DEBUG: Instructor view started";
    $todosLosEventos = $eventoBusiness->getAllEventos();
    $misEventos = array_filter($todosLosEventos, function ($evento) use ($usuarioId) {
        return $evento->getInstructorId() == $usuarioId;
    });
    ?>
        <div class="section">
            <h3>Mis Eventos Asignados</h3>
            <?php if (empty($misEventos)): ?>
                <p>No tiene eventos asignados.</p>
            <?php else:
                foreach ($misEventos as $evento): ?>
                    <h4><?= htmlspecialchars($evento->getNombre()) ?> (<?= $evento->getFecha() ?>)</h4>
                    <p>Horario: <?= $evento->getHoraInicio() ?> a <?= $evento->getHoraFin() ?></p>
                    <p>Clientes inscritos:</p>
                    <ul>
                        <?php
                        $reservasDelEvento = $reservaBusiness->getReservasPorEvento($evento->getId());
                        if (empty($reservasDelEvento)) {
                            echo "<li>Aún no hay clientes inscritos.</li>";
                        } else {
                            foreach ($reservasDelEvento as $reserva) {
                                $clienteInfo = $clienteBusiness->getClientePorId($reserva->getClienteId());
                                if ($clienteInfo) {
                                    echo "<li>" . htmlspecialchars($clienteInfo->getNombre()) . "</li>";
                                }
                            }
                        }
                        ?>
                    </ul>
                    <hr>
                <?php endforeach;
            endif; ?>
        </div>

    <?php
    // ====================================================================
    // VISTA DE CLIENTE
    // ====================================================================
    elseif ($tipoUsuario == 'cliente'):
    echo "DEBUG: Client view started";
    $eventos = $eventoBusiness->getAllEventos(true);
    $diasCerrados = $reglasGimnasio['DIAS_CERRADOS_ESPECIALES'];
    $diasActivos = [];
    $todosLosHorarios = $horarioBusiness->getAllHorarios();
    foreach ($todosLosHorarios as $h) {
        if ($h->isActivo()) {
            $diasActivos[] = ($h->getId() % 7);
        }
    }
    ?>
        <h3>Realizar una Reserva</h3>

        <form action="../action/reservaAction.php" method="post">
            <input type="hidden" name="cliente_id" value="<?= $usuarioId ?>">

            <p><strong>Paso 1:</strong> Seleccione el tipo de reserva.</p>
            <input type="radio" id="tipo_libre" name="tipo_reserva" value="libre" checked>
            <label for="tipo_libre">Uso Libre de Instalaciones</label>
            <br>
            <input type="radio" id="tipo_evento" name="tipo_reserva" value="evento">
            <label for="tipo_evento">Inscribirse a un Evento</label>

            <div id="selector-evento" style="display:none;">
                <p><strong>Paso 2:</strong> Seleccione el evento.</p>
                <select name="evento_id">
                    <option value="">-- Seleccione un evento --</option>
                    <?php foreach ($eventos as $evento): ?>
                        <option value="<?= $evento->getId() ?>">
                            <?= htmlspecialchars($evento->getNombre()) ?> (<?= $evento->getFecha() ?>
                            de <?= $evento->getHoraInicio() ?> a <?= $evento->getHoraFin() ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="selector-fecha-hora">
                <p><strong>Paso 2:</strong> Seleccione la fecha.</p>
                <input type="date" id="fecha" name="fecha" required>

                <p><strong>Paso 3:</strong> Seleccione la hora de inicio.</p>
                <div id="slots-disponibles">
                    <p>Seleccione una fecha para ver los horarios disponibles.</p>
                </div>
                <input type="hidden" id="hora_inicio" name="hora_inicio" value="">
            </div>

            <hr>
            <button type="submit" name="crear_reserva">Confirmar Reserva</button>
        </form>

        <script>
            const radioLibre = document.getElementById('tipo_libre');
            const radioEvento = document.getElementById('tipo_evento');
            const selectorEvento = document.getElementById('selector-evento');
            const selectorFechaHora = document.getElementById('selector-fecha-hora');

            radioLibre.addEventListener('change', () => {
                selectorEvento.style.display = 'none';
                selectorFechaHora.style.display = 'block';
            });

            radioEvento.addEventListener('change', () => {
                selectorEvento.style.display = 'block';
                selectorFechaHora.style.display = 'none';
            });

            const fechaInput = document.getElementById('fecha');
            const slotsContainer = document.getElementById('slots-disponibles');
            const horaInicioInput = document.getElementById('hora_inicio');

            const hoy = new Date();
            const fechaMax = new Date();
            fechaMax.setDate(hoy.getDate() + <?= $reglasGimnasio['MAX_DIAS_ANTICIPACION'] ?>);

            fechaInput.min = hoy.toISOString().split('T')[0];
            fechaInput.max = fechaMax.toISOString().split('T')[0];

            const diasActivos = <?= json_encode($diasActivos) ?>;
            const diasCerradosEsp = <?= json_encode($diasCerrados) ?>;

            fechaInput.addEventListener('input', function () {
                const fecha = new Date(this.value);
                const diaSemana = fecha.getUTCDay();
                const fechaStr = this.value;

                if (!diasActivos.includes(diaSemana) || diasCerradosEsp.includes(fechaStr)) {
                    this.classList.add('dia-no-disponible');
                    slotsContainer.innerHTML = '<p style="color:red;">El gimnasio está cerrado en la fecha seleccionada.</p>';
                    return;
                } else {
                    this.classList.remove('dia-no-disponible');
                }

                slotsContainer.innerHTML = '<p>Cargando horarios...</p>';
                fetch(`../ajax/getDisponibilidad.php?fecha=${fechaStr}`)
                    .then(response => response.json())
                    .then(data => {
                        slotsContainer.innerHTML = '';
                        if (data.error) {
                            slotsContainer.innerHTML = `<p style="color:red;">${data.error}</p>`;
                        } else if (!data.estaAbierto || data.slots.length === 0) {
                            slotsContainer.innerHTML = '<p>No hay horarios disponibles para este día.</p>';
                        } else {
                            data.slots.forEach(slot => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.textContent = slot.substring(0, 5);
                                btn.onclick = () => {
                                    document.querySelectorAll('#slots-disponibles button').forEach(b => b.style.backgroundColor = '');
                                    btn.style.backgroundColor = '#a0d2eb';
                                    horaInicioInput.value = slot;
                                };
                                slotsContainer.appendChild(btn);
                            });
                        }
                    });
            });
        </script>
        <?php
        echo "DEBUG: End of client view";
    endif;
    echo "DEBUG: End of role-based view section";
    ?>

</div>

</body>
</html>