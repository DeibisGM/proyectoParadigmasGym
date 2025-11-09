<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: loginView.php");
    exit();
}

include_once '../business/horarioBusiness.php';
include_once '../business/horarioLibreBusiness.php';
include_once '../business/instructorBusiness.php';
include_once '../business/reservaBusiness.php';

$tipoUsuario = $_SESSION['tipo_usuario'];
$esAdmin = ($tipoUsuario === 'admin');
$clienteId = $_SESSION['usuario_id'];

$timestamp = isset($_GET['semana']) ? strtotime($_GET['semana']) : time();
$diaSemanaActual = date('N', $timestamp);
$inicioSemana = (new DateTime(date('Y-m-d', $timestamp)))->modify('-' . ($diaSemanaActual - 1) . ' days');
$finSemana = (clone $inicioSemana)->modify('+6 days');
$semanaAnterior = (clone $inicioSemana)->modify('-7 days')->format('Y-m-d');
$semanaSiguiente = (clone $inicioSemana)->modify('+7 days')->format('Y-m-d');
$semanaActual = $inicioSemana->format('d/m/Y') . ' - ' . $finSemana->format('d/m/Y');

$horarioBusiness = new HorarioBusiness();
$horariosSemanales = $horarioBusiness->getAllHorarios();
$horarioLibreBusiness = new HorarioLibreBusiness();
$horariosLibresCreados = $horarioLibreBusiness->getHorariosPorRangoDeFechas($inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d'));
$instructorBusiness = new InstructorBusiness();
$instructores = $instructorBusiness->getAllTBInstructor(true);

$mapaHorariosSemanales = [];
foreach ($horariosSemanales as $h) {
    $mapaHorariosSemanales[$h->getId()] = $h;
}

$mapaHorariosLibres = [];
$mapaInstructores = [];
foreach ($instructores as $i) {
    $mapaInstructores[$i->getInstructorId()] = $i->getInstructorNombre();
}
foreach ($horariosLibresCreados as $hl) {
    $key = $hl->getFecha() . '_' . date('H', strtotime($hl->getHora()));
    $mapaHorariosLibres[$key] = $hl;
}

$misReservasLookup = [];
if ($tipoUsuario === 'cliente') {
    $reservaBusiness = new ReservaBusiness();
    $misReservas = $reservaBusiness->getReservasLibrePorCliente($clienteId);
    if (!empty($misReservas)) {
        foreach ($misReservas as $reserva) {
            $misReservasLookup[$reserva->getHorarioLibreId()] = $reserva->getId();
        }
    }
}

$horaMinima = 24;
$horaMaxima = 0;
foreach ($horariosSemanales as $horario) {
    if ($horario->isActivo()) {
        $apertura = (int) date('H', strtotime($horario->getApertura()));
        $cierre = (int) date('H', strtotime($horario->getCierre()));
        if ($apertura < $horaMinima)
            $horaMinima = $apertura;
        if ($cierre > $horaMaxima)
            $horaMaxima = $cierre;
    }
}
if ($horaMinima >= $horaMaxima) {
    $horaMinima = 8;
    $horaMaxima = 20;
}

$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Horario de Uso Libre</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Gestión de Horario Libre</h2>
        </header>

        <main>
            <section class="navegacion-semana">
                <a href="?semana=<?php echo $semanaAnterior; ?>"><button><i class="ph ph-caret-left"></i></button></a>
                <h3>
                    <?php echo $semanaActual; ?>
                </h3>
                                <a href="?semana=<?php echo $semanaSiguiente; ?>"><button><i
                                                class="ph ph-caret-right"></i></button></a>            </section>

            <section class="grid-container">
                <table class="grid-horario">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <?php
                            $fechaTemp = clone $inicioSemana;
                            foreach ($dias as $dia) {
                                echo '<th>' . $dia . '<br><small>' . $fechaTemp->format('d/m') . '</small></th>';
                                $fechaTemp->modify('+1 day');
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($hora = $horaMinima; $hora < $horaMaxima; $hora++): ?>
                            <tr>
                                <td class="hora-label">
                                    <?php echo str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00'; ?>
                                </td>
                                <?php
                                $fechaCelda = clone $inicioSemana;
                                for ($d = 1; $d <= 7; $d++):
                                    $horarioDelDia = $mapaHorariosSemanales[$d] ?? null;
                                    $clase = 'deshabilitado';
                                    $contenido = '';
                                    $dataAttr = '';

                                    $estaAbierto = ($horarioDelDia && $horarioDelDia->isActivo() && $hora >= date('H', strtotime($horarioDelDia->getApertura())) && $hora < date('H', strtotime($horarioDelDia->getCierre())));
                                    $estaBloqueado = false;
                                    if ($estaAbierto) {
                                        foreach ($horarioDelDia->getBloqueos() as $bloqueo) {
                                            if ($hora >= date('H', strtotime($bloqueo['inicio'])) && $hora < date('H', strtotime($bloqueo['fin']))) {
                                                $estaBloqueado = true;
                                                break;
                                            }
                                        }
                                    }

                                    if ($estaAbierto && !$estaBloqueado) {
                                        $fechaHoraKey = $fechaCelda->format('Y-m-d') . '_' . str_pad($hora, 2, '0', STR_PAD_LEFT);
                                        $dataAttr = 'data-fecha-hora="' . $fechaCelda->format('Y-m-d') . ' ' . str_pad($hora, 2, '0', STR_PAD_LEFT) . '"';

                                        if (isset($mapaHorariosLibres[$fechaHoraKey])) {
                                            $slot = $mapaHorariosLibres[$fechaHoraKey];
                                            $disponibles = $slot->getCupos() - $slot->getMatriculados();
                                            $esMiReserva = ($tipoUsuario === 'cliente' && isset($misReservasLookup[$slot->getId()]));
                                            $dataAttr .= ' data-id="' . $slot->getId() . '"';

                                            if ($esMiReserva) {
                                                $clase = 'reservado-por-mi';
                                                $reservaId = $misReservasLookup[$slot->getId()];
                                                $contenido = '<div class="slot-content"><span class="slot-info">RESERVADO</span><button type="button" class="btn-cancelar-slot-icon" data-reserva-id="' . $reservaId . '"><i class="ph ph-x"></i></button></div>';
                                            } else {
                                                $clase = ($disponibles > 0) ? 'creado disponible-cliente' : 'creado lleno';
                                                $instructorNombre = $mapaInstructores[$slot->getInstructorId()] ?? 'N/A';
                                                $contenido = '<div class="slot-content"><span class="slot-info">' . $instructorNombre . '<br>Cupos: ' . $disponibles . '/' . $slot->getCupos() . '</span>';
                                                if ($esAdmin) {
                                                    $contenido .= '<form method="POST" action="../action/horarioLibreAction.php" class="delete-form" onsubmit="return confirm(\'¿Eliminar este espacio?\');"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="' . $slot->getId() . '"><button type="submit" class="btn-delete-slot"><i class="ph ph-x"></i></button></form>';
                                                }
                                                $contenido .= '</div>';
                                            }
                                        } else {
                                            if ($esAdmin) {
                                                $clase = 'disponible-admin';
                                            }
                                        }
                                    }

                                    echo '<td class="celda-horario ' . $clase . '" ' . $dataAttr . '>' . $contenido . '</td>';
                                    $fechaCelda->modify('+1 day');
                                endfor;
                                ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </section>

            <?php if ($esAdmin): ?>
                <section id="panel-admin-creacion" style="display:none;">
                    <h3><i class="ph ph-plus-circle"></i> Crear Nuevos Espacios</h3>
                    <p>Se crearán <strong id="contador-seleccion">0</strong> espacios en las celdas seleccionadas.</p>
                    <form id="form-crear-slots" method="POST" action="../action/horarioLibreAction.php">
                        <input type="hidden" name="accion" value="crear">
                        <div id="slots-seleccionados-container"></div>
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="instructorId">Instructor:</label>
                                <select id="instructorId" name="instructorId" required>
                                    <option value="">-- Elige un instructor --</option>
                                    <?php foreach ($instructores as $instructor): ?>
                                        <option value="<?php echo $instructor->getInstructorId(); ?>">
                                            <?php echo htmlspecialchars($instructor->getInstructorNombre()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cupos">Cupos por Hora:</label>
                                <input type="number" id="cupos" name="cupos" min="1" required>
                            </div>
                        </div>
                        <button type="submit"><i class="ph ph-check-circle"></i> Confirmar</button>
                        <button type="button" id="btn-limpiar-seleccion" class="btn-danger">Limpiar</button>
                    </form>
                </section>
            <?php else: ?>
                <section id="panel-cliente-reserva" style="display: none;">
                    <h3><i class="ph ph-check-square"></i> Confirmar Reservas</h3>
                    <p>Se van a reservar <strong id="contador-seleccion-cliente">0</strong> espacios.</p>
                    <form id="form-reservar-libre">
                        <div id="slots-seleccionados-cliente-container"></div>
                        <div class="form-grid-container">
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label style="flex-direction: row; align-items: center;">
                                    <input type="checkbox" name="incluirme" checked
                                        style="width: auto; height: auto; margin-right: 0.5rem;">
                                    Incluirme en la reserva
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="ids_invitados">IDs de miembros invitados:</label>
                                <input type="text" name="ids_invitados" id="ids_invitados" placeholder="Ej: 2, 8, 15">
                            </div>
                        </div>
                        <button type="submit" id="btn-confirmar-reserva"><i class="ph ph-calendar-plus"></i>
                            Reservar</button>
                        <button type="button" id="btn-limpiar-seleccion-cliente" class="btn-danger">Limpiar</button>
                    </form>
                </section>
            <?php endif; ?>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const esAdmin = <?php echo json_encode($esAdmin); ?>;
            const tipoUsuario = <?php echo json_encode($tipoUsuario); ?>;

            if (esAdmin) setupAdminInteractions();
            if (tipoUsuario === 'cliente') setupClientInteractions();
        });

        function setupAdminInteractions() {
            const grid = document.querySelector('.grid-horario');
            const panel = document.getElementById('panel-admin-creacion');
            const contador = document.getElementById('contador-seleccion');
            const container = document.getElementById('slots-seleccionados-container');
            const btnLimpiar = document.getElementById('btn-limpiar-seleccion');

            grid.addEventListener('click', function (e) {
                const cell = e.target.closest('.disponible-admin');
                if (cell) {
                    cell.classList.toggle('seleccionado');
                    updateAdminPanel();
                }
            });

            btnLimpiar.addEventListener('click', function () {
                document.querySelectorAll('.celda-horario.seleccionado').forEach(cell => {
                    cell.classList.remove('seleccionado');
                });
                updateAdminPanel();
            });

            function updateAdminPanel() {
                const seleccionados = document.querySelectorAll('.celda-horario.seleccionado');
                contador.textContent = seleccionados.length;
                container.innerHTML = '';
                seleccionados.forEach(cell => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'slots[]';
                    input.value = cell.dataset.fechaHora;
                    container.appendChild(input);
                });
                panel.style.display = seleccionados.length > 0 ? 'block' : 'none';
            }
        }

        function setupClientInteractions() {
            const grid = document.querySelector('.grid-horario');
            const panel = document.getElementById('panel-cliente-reserva');
            const contador = document.getElementById('contador-seleccion-cliente');
            const formReserva = document.getElementById('form-reservar-libre');
            const container = document.getElementById('slots-seleccionados-cliente-container');
            const btnLimpiar = document.getElementById('btn-limpiar-seleccion-cliente');

            grid.addEventListener('click', function (e) {
                const cell = e.target.closest('.celda-horario.disponible-cliente');
                if (cell) {
                    cell.classList.toggle('seleccionado');
                    updateClientPanel();
                    return;
                }

                const cancelButton = e.target.closest('.btn-cancelar-slot-icon');
                if (cancelButton) {
                    e.stopPropagation();
                    const reservaId = cancelButton.dataset.reservaId;
                    if (confirm('¿Desea cancelar esta reserva?')) {
                        const formData = new FormData();
                        formData.append('action', 'cancel_libre');
                        formData.append('reservaId', reservaId);

                        fetch('../action/reservaAction.php', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(data => {
                                alert(data.message);
                                if (data.success) {
                                    window.location.reload();
                                }
                            })
                            .catch(err => alert("Error de conexión."));
                    }
                }
            });

            btnLimpiar.addEventListener('click', function () {
                document.querySelectorAll('.celda-horario.seleccionado').forEach(cell => {
                    cell.classList.remove('seleccionado');
                });
                updateClientPanel();
            });

            formReserva.addEventListener('submit', function (e) {
                e.preventDefault();
                const seleccionados = document.querySelectorAll('.celda-horario.seleccionado');
                if (seleccionados.length === 0) {
                    alert("Por favor, selecciona al menos un horario.");
                    return;
                }

                const formData = new FormData(formReserva);
                formData.append('action', 'create_libre');

                fetch('../action/reservaAction.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) window.location.reload();
                    })
                    .catch(err => alert("Error de conexión."));
            });

            function updateClientPanel() {
                const seleccionados = document.querySelectorAll('.celda-horario.seleccionado');
                contador.textContent = seleccionados.length;
                container.innerHTML = '';
                seleccionados.forEach(cell => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'horarioLibreIds[]';
                    input.value = cell.dataset.id;
                    container.appendChild(input);
                });

                panel.style.display = seleccionados.length > 0 ? 'block' : 'none';
            }
        }
    </script>
</body>

</html>