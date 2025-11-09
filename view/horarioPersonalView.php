<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: loginView.php");
    exit();
}

include_once '../business/horarioPersonalBusiness.php';
include_once '../business/instructorBusiness.php';

$tipoUsuario = $_SESSION['tipo_usuario'];
$esAdmin = ($tipoUsuario === 'admin');
$esInstructor = ($tipoUsuario === 'instructor');
$esCliente = ($tipoUsuario === 'cliente');
$usuarioId = $_SESSION['usuario_id'];

$horarioPersonalBusiness = new HorarioPersonalBusiness();
$instructorBusiness = new InstructorBusiness();

if ($esCliente) {
    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d', strtotime('+1 month'));
    $horariosDisponibles = $horarioPersonalBusiness->getHorariosDisponibles($fechaInicio, $fechaFin);
    $misReservas = $horarioPersonalBusiness->getMisReservasPersonales($usuarioId);
}

if ($esAdmin || $esInstructor) {
    $timestamp = isset($_GET['semana']) ? strtotime($_GET['semana']) : time();
    $diaSemanaActual = date('N', $timestamp);
    $inicioSemana = (new DateTime(date('Y-m-d', $timestamp)))->modify('-' . ($diaSemanaActual - 1) . ' days');
    $finSemana = (clone $inicioSemana)->modify('+6 days');
    $semanaAnterior = (clone $inicioSemana)->modify('-7 days')->format('Y-m-d');
    $semanaSiguiente = (clone $inicioSemana)->modify('+7 days')->format('Y-m-d');
    $semanaActual = $inicioSemana->format('d/m/Y') . ' - ' . $finSemana->format('d/m/Y');

    $horariosPersonales = ($esInstructor) ?
        $horarioPersonalBusiness->getHorariosPorInstructor($usuarioId, $inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d')) :
        $horarioPersonalBusiness->getHorariosDisponibles($inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d'));

    $instructores = $instructorBusiness->getAllTBInstructor(true);

    $mapaHorariosPersonales = [];
    foreach ($horariosPersonales as $horario) {
        $key = $horario->getFecha() . '_' . substr($horario->getHora(), 0, 2);
        $mapaHorariosPersonales[$key] = $horario;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Personal - Reservas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Instructor Personal - Reservas</h2>
        </header>

        <main>
            <?php if ($esCliente): ?>
                <section>
                    <h3><i class="ph ph-calendar-check"></i> Mis Reservas Activas</h3>
                    <?php if (empty($misReservas)): ?>
                        <p>No tienes reservas activas.</p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-clients">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Instructor</th>
                                        <th>Duración</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($misReservas as $reserva): ?>
                                        <tr>
                                            <td data-label="Fecha">
                                                <?php echo date('d/m/Y', strtotime($reserva->getFecha())); ?>
                                            </td>
                                            <td data-label="Hora">
                                                <?php echo substr($reserva->getHora(), 0, 5); ?>
                                            </td>
                                            <td data-label="Instructor">
                                                <?php echo htmlspecialchars($reserva->getInstructorNombre()); ?>
                                            </td>
                                            <td data-label="Duración">
                                                <?php echo $reserva->getDuracion(); ?> minutos
                                            </td>
                                            <td data-label="Acciones" class="actions">
                                                <button class="btn-row btn-danger"
                                                    onclick="cancelarReserva(<?php echo $reserva->getId(); ?>)">
                                                    <i class="ph ph-x"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

                <section>
                    <h3><i class="ph ph-calendar-plus"></i> Horarios Disponibles para Reservar</h3>
                    <?php if (empty($horariosDisponibles)): ?>
                        <p>No hay horarios disponibles para reservar.</p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-clients">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Instructor</th>
                                        <th>Duración</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($horariosDisponibles as $horario): ?>
                                        <tr>
                                            <td data-label="Fecha">
                                                <?php echo date('d/m/Y', strtotime($horario->getFecha())); ?>
                                            </td>
                                            <td data-label="Hora">
                                                <?php echo substr($horario->getHora(), 0, 5); ?>
                                            </td>
                                            <td data-label="Instructor">
                                                <?php echo htmlspecialchars($horario->getInstructorNombre()); ?>
                                            </td>
                                            <td data-label="Duración">
                                                <?php echo $horario->getDuracion(); ?> minutos
                                            </td>
                                            <td data-label="Estado">
                                                <span
                                                    class="badge-soft <?= $horario->getEstado() === 'disponible' ? 'disponible' : 'ocupado' ?>">
                                                    <?= $horario->getEstado() === 'disponible' ? 'Disponible' : 'Ocupado' ?>
                                                </span>
                                            </td>
                                            <td data-label="Acciones" class="actions">
                                                <?php if ($horario->getEstado() === 'disponible'): ?>
                                                    <button class="btn-row"
                                                        onclick="reservarHorario(<?php echo $horario->getId(); ?>)">
                                                        <i class="ph ph-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <section class="navegacion-semana">
                    <a href="?semana=<?php echo $semanaAnterior; ?>"><button><i class="ph ph-caret-left"></i>
                            Anterior</button></a>
                    <h3>
                        <?php echo $semanaActual; ?>
                    </h3>
                    <a href="?semana=<?php echo $semanaSiguiente; ?>"><button>Siguiente <i
                                class="ph ph-caret-right"></i></button></a>
                </section>

                <section>
                    <h3><i class="ph ph-plus-circle"></i> Crear Horarios Personales</h3>
                    <form id="formCrearHorarios">
                        <input type="hidden" name="action" value="crear_horarios">
                        <div class="form-grid-container">
                            <?php if ($esAdmin): ?>
                                <div class="form-group">
                                    <label for="instructorId">Instructor:</label>
                                    <select name="instructorId" id="instructorId" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach ($instructores as $inst): ?>
                                            <option value="<?php echo $inst->getInstructorId(); ?>">
                                                <?php echo $inst->getInstructorNombre(); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="instructorId" value="<?php echo $usuarioId; ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="fecha">Fecha:</label>
                                <input type="date" name="fecha" id="fecha" required
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="duracion">Duración (minutos):</label>
                                <input type="number" name="duracion" id="duracion" value="60" min="30" max="120">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 1rem;">
                            <label>Horarios a crear:</label>
                            <div class="horarios-checkbox"
                                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 1rem;">
                                <?php for ($hora = 8; $hora < 20; $hora++): ?>
                                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                                        <input type="checkbox" name="horarios[]"
                                            value="<?php echo str_pad($hora, 2, '0', STR_PAD_LEFT); ?>:00"
                                            style="width: auto; height: auto;">
                                        <?php echo str_pad($hora, 2, '0', STR_PAD_LEFT); ?>:00
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <button type="submit">Crear Horarios</button>
                    </form>
                </section>

                <section class="grid-container">
                    <table class="grid-horario">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <?php
                                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                $fechaTemp = clone $inicioSemana;
                                foreach ($dias as $dia) {
                                    echo '<th>' . $dia . '<br><small>' . $fechaTemp->format('d/m') . '</small></th>';
                                    $fechaTemp->modify('+1 day');
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($hora = 8; $hora < 20; $hora++): ?>
                                <tr>
                                    <td class="hora-label">
                                        <?php echo str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00'; ?>
                                    </td>
                                    <?php
                                    $fechaCelda = clone $inicioSemana;
                                    for ($d = 1; $d <= 7; $d++):
                                        $fechaHoraKey = $fechaCelda->format('Y-m-d') . '_' . str_pad($hora, 2, '0', STR_PAD_LEFT);
                                        $clase = 'deshabilitado';
                                        $contenido = '';
                                        if (isset($mapaHorariosPersonales[$fechaHoraKey])) {
                                            $slot = $mapaHorariosPersonales[$fechaHoraKey];
                                            $instructorNombre = $slot->getInstructorNombre() ?? 'ID ' . $slot->getInstructorId();
                                            if ($slot->getEstado() === 'reservado') {
                                                $clase = 'creado lleno';
                                                $clienteNombre = $slot->getClienteNombre() ?? 'ID ' . $slot->getClienteId();
                                                $contenido = '<div class="slot-info"><strong>RESERVADO</strong><br><small>' . $instructorNombre . '</small><br><small>Por: ' . $clienteNombre . '</small></div>';
                                            } else {
                                                $clase = 'creado disponible-cliente';
                                                $contenido = '<div class="slot-info"><strong>DISPONIBLE</strong><br><small>' . $instructorNombre . '</small></div>';
                                            }
                                        }
                                        echo '<td class="celda-horario ' . $clase . '">' . $contenido . '</td>';
                                        $fechaCelda->modify('+1 day');
                                    endfor;
                                    ?>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </main>
    </div>
    <script>
        function reservarHorario(horarioId) {
            if (confirm('¿Confirmar reserva de instructor personal?')) {
                const formData = new FormData();
                formData.append('action', 'reservar_personal');
                formData.append('horarioId', horarioId);
                fetchAction(formData);
            }
        }
        function cancelarReserva(horarioId) {
            if (confirm('¿Cancelar reserva de instructor personal?')) {
                const formData = new FormData();
                formData.append('action', 'cancelar_personal');
                formData.append('horarioId', horarioId);
                fetchAction(formData);
            }
        }
        <?php if ($esAdmin || $esInstructor): ?>
            document.getElementById('formCrearHorarios')?.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetchAction(formData);
            });
        <?php endif; ?>
        function fetchAction(formData) {
            fetch('../action/horarioPersonalAction.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>