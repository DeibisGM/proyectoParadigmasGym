<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: loginView.php");
    exit();
}

include_once '../business/horarioBusiness.php';
include_once '../business/horarioLibreBusiness.php';
include_once '../business/instructorBusiness.php';
include_once '../business/reservaBusiness.php'; // For getting user's reservations

$tipoUsuario = $_SESSION['tipo_usuario'];
$esAdmin = ($tipoUsuario === 'admin');

// --- Lógica de Fechas para la Semana ---
$timestamp = isset($_GET['semana']) ? strtotime($_GET['semana']) : time();
$diaSemanaActual = date('N', $timestamp);
$inicioSemana = (new DateTime(date('Y-m-d', $timestamp)))->modify('-' . ($diaSemanaActual - 1) . ' days');
$finSemana = (clone $inicioSemana)->modify('+6 days');

$semanaAnterior = (clone $inicioSemana)->modify('-7 days')->format('Y-m-d');
$semanaSiguiente = (clone $inicioSemana)->modify('+7 days')->format('Y-m-d');
$semanaActual = $inicioSemana->format('d/m/Y') . ' - ' . $finSemana->format('d/m/Y');

// --- Carga de Datos ---
$horarioBusiness = new HorarioBusiness();
$horariosSemanales = $horarioBusiness->getAllHorarios();

$horarioLibreBusiness = new HorarioLibreBusiness();
$horariosLibresCreados = $horarioLibreBusiness->getHorariosPorRangoDeFechas($inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d'));

$instructorBusiness = new InstructorBusiness();
$instructores = $instructorBusiness->getAllTBInstructor(true);

// --- Procesar Datos para la Vista ---
$mapaHorariosSemanales = [];
foreach($horariosSemanales as $h) {
    $mapaHorariosSemanales[$h->getId()] = $h;
}

$mapaHorariosLibres = [];
$mapaInstructores = [];
foreach($instructores as $i) {
    $mapaInstructores[$i->getInstructorId()] = $i->getInstructorNombre();
}
foreach($horariosLibresCreados as $hl) {
    $key = $hl->getFecha() . '_' . date('H', strtotime($hl->getHora()));
    $mapaHorariosLibres[$key] = $hl;
}

// --- Fetch user's reservations for visual feedback ---
$misReservasLookup = [];
if ($tipoUsuario === 'cliente') {
    $reservaBusiness = new ReservaBusiness();
    $misReservas = $reservaBusiness->getReservasLibrePorCliente($_SESSION['usuario_id']);
    foreach ($misReservas as $reserva) {
        $misReservasLookup[$reserva->getHorarioLibreId()] = $reserva->getId();
    }
}

// Lógica para Determinar Rango de Horas Dinámico
$horaMinima = 24;
$horaMaxima = 0;
foreach ($horariosSemanales as $horario) {
    if ($horario->isActivo()) {
        $apertura = (int)date('H', strtotime($horario->getApertura()));
        $cierre = (int)date('H', strtotime($horario->getCierre()));
        if ($apertura < $horaMinima) $horaMinima = $apertura;
        if ($cierre > $horaMaxima) $horaMaxima = $cierre;
    }
}
if ($horaMinima >= $horaMaxima) { // Fallback por si no hay horarios definidos
    $horaMinima = 8;
    $horaMaxima = 20;
}

$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Horario de Uso Libre</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .grid-container { overflow-x: auto; }
        .grid-horario { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .grid-horario th, .grid-horario td { border: 1px solid #dee2e6; padding: 8px; text-align: center; height: 70px; }
        .hora-label { font-weight: bold; vertical-align: middle; background-color: #f8f9fa;}
        .celda-horario { vertical-align: middle; font-size: 0.85em; position: relative; }
        .reservado-por-mi { background-color: #d4edda !important; border-color: #c3e6cb !important; }
        .reservado-por-mi span { color: #155724; font-weight: bold; }
        .btn-cancelar-slot { cursor: pointer; background-color: #ffc107; color: #212529; border: 1px solid #e0a800; padding: 2px 5px; font-size: 0.9em; border-radius: 3px; }
        .btn-delete-slot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 22px;
            height: 22px;
            padding: 0;
            background-color: #dc3545;
            color: white;
            border: 1px solid #c82333;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-delete-slot:hover { background-color: #c82333; }
        #btn-limpiar-seleccion {
            background-color: #6c757d;
            color: white;
            border-color: #5a6268;
        }
        #btn-limpiar-seleccion:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i> Volver al Inicio</a><br><br>
        <h2><i class="ph ph-calendar-grid"></i> Horario de Uso Libre</h2>
    </header>
    <hr>
    <main>
        <div class="navegacion-semana">
            <a href="?semana=<?php echo $semanaAnterior; ?>"><button><i class="ph ph-caret-left"></i> Semana Anterior</button></a>
            <h3><?php echo $semanaActual; ?></h3>
            <a href="?semana=<?php echo $semanaSiguiente; ?>"><button>Semana Siguiente <i class="ph ph-caret-right"></i></button></a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <p class="success">Acción realizada con éxito.</p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error">Error: <?= htmlspecialchars(urldecode($_GET['error'])) ?></p>
        <?php endif; ?>

        <div class="grid-container">
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
                        <td class="hora-label"><?php echo str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00'; ?></td>
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
                                    $inicioBloqueo = date('H', strtotime($bloqueo['inicio']));
                                    $finBloqueo = date('H', strtotime($bloqueo['fin']));
                                    if ($hora >= $inicioBloqueo && $hora < $finBloqueo) {
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
                                    $instructorNombre = $mapaInstructores[$slot->getInstructorId()] ?? 'N/A';
                                    $disponibles = $slot->getCupos() - $slot->getMatriculados();

                                    $esMiReserva = ($tipoUsuario === 'cliente' && isset($misReservasLookup[$slot->getId()]));

                                    if ($esMiReserva) {
                                        $clase = 'reservado-por-mi';
                                        $reservaId = $misReservasLookup[$slot->getId()];
                                        $contenido = '<span>RESERVADO</span><br><button class="btn-cancelar-slot" data-reserva-id="' . $reservaId . '">Cancelar</button>';
                                    } else {
                                        $clase = 'creado';
                                        if ($disponibles <= 0) {
                                            $clase .= ' lleno';
                                        } elseif ($tipoUsuario == 'cliente') {
                                            $clase .= ' disponible-cliente';
                                        }
                                        $contenido = "<span>{$instructorNombre}<br>Cupos: {$disponibles}/{$slot->getCupos()}</span>";
                                    }

                                    if ($esAdmin) {
                                        $contenido .= '<form method="POST" action="../action/horarioLibreAction.php" style="display:inline;">
                                                         <input type="hidden" name="accion" value="eliminar">
                                                         <input type="hidden" name="id" value="' . $slot->getId() . '">
                                                         <button type="submit" title="Eliminar Espacio" onclick="return confirm(\'¿Eliminar este espacio y sus reservas asociadas?\');" class="btn-delete-slot">X</button>
                                                       </form>';
                                    }
                                    $dataAttr .= ' data-id="' . $slot->getId() . '"';
                                } else {
                                    if ($esAdmin) $clase = 'disponible-admin';
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
        </div>

        <?php if ($esAdmin): ?>
            <section id="panel-admin-creacion">
                <h3><i class="ph ph-plus-circle"></i> Crear Nuevos Espacios</h3>
                <p>Se crearán <strong id="contador-seleccion">0</strong> espacios en las celdas seleccionadas.</p>
                <form id="form-crear-slots" method="POST" action="../action/horarioLibreAction.php">
                    <input type="hidden" name="accion" value="crear">
                    <div id="slots-seleccionados-container"></div>

                    <label for="instructorId">Seleccionar Instructor:</label>
                    <select id="instructorId" name="instructorId" required>
                        <option value="">-- Elige un instructor --</option>
                        <?php foreach($instructores as $instructor): ?>
                            <option value="<?php echo $instructor->getInstructorId(); ?>"><?php echo htmlspecialchars($instructor->getInstructorNombre()); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="cupos">Cantidad de Cupos por Hora:</label>
                    <input type="number" id="cupos" name="cupos" min="1" required>

                    <button type="submit"><i class="ph ph-check-circle"></i> Confirmar Creación</button>
                    <button type="button" id="btn-limpiar-seleccion">Limpiar Selección</button>
                </form>
            </section>
        <?php else: ?>
            <section id="panel-cliente-reserva" style="display: none; margin-top: 20px;">
                <h3><i class="ph ph-check-square"></i> Confirmar Reservas</h3>
                <p>Se van a reservar <strong id="contador-seleccion-cliente">0</strong> espacios de tiempo.</p>
                <button id="btn-confirmar-reserva"><i class="ph ph-calendar-plus"></i> Reservar Seleccionados</button>
                <button type="button" id="btn-limpiar-seleccion-cliente">Limpiar Selección</button>
            </section>
            <p style="margin-top: 10px;"><strong>Instrucciones:</strong> Haz clic en los espacios verdes para seleccionarlos. Luego, haz clic en "Reservar Seleccionados".</p>
        <?php endif; ?>

    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        grid.addEventListener('click', function(e) {
            const cell = e.target.closest('.disponible-admin');
            if (cell) {
                cell.classList.toggle('seleccionado');
                updateAdminPanel();
            }
        });

        btnLimpiar.addEventListener('click', function() {
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
        const btnConfirmar = document.getElementById('btn-confirmar-reserva');
        const btnLimpiar = document.getElementById('btn-limpiar-seleccion-cliente');

        grid.addEventListener('click', function(e) {
            // Handle selection of available slots
            const cell = e.target.closest('.celda-horario.disponible-cliente');
            if (cell) {
                cell.classList.toggle('seleccionado');
                updateClientPanel();
                return; // Stop processing if it's a selection click
            }

            // Handle cancellation click
            const cancelButton = e.target.closest('.btn-cancelar-slot');
            if (cancelButton) {
                e.stopPropagation(); // Prevent the cell selection logic from firing
                const reservaId = cancelButton.dataset.reservaId;
                if (confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
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
                        .catch(err => alert("Error de conexión al cancelar."));
                }
            }
        });

        btnLimpiar.addEventListener('click', function() {
            document.querySelectorAll('.celda-horario.seleccionado').forEach(cell => {
                cell.classList.remove('seleccionado');
            });
            updateClientPanel();
        });

        btnConfirmar.addEventListener('click', function() {
            const seleccionados = document.querySelectorAll('.celda-horario.seleccionado');
            if (seleccionados.length === 0) {
                alert("Por favor, selecciona al menos un horario.");
                return;
            }

            const ids = Array.from(seleccionados).map(cell => cell.dataset.id);

            const formData = new FormData();
            formData.append('action', 'create');
            ids.forEach(id => formData.append('horarioLibreIds[]', id));

            fetch('../action/reservaAction.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    // This will be improved in the next step to show detailed results
                    alert(data.message);
                    if(data.success) window.location.reload();
                })
                .catch(err => alert("Error de conexión."));
        });

        function updateClientPanel() {
            const seleccionados = document.querySelectorAll('.celda-horario.seleccionado');
            contador.textContent = seleccionados.length;
            panel.style.display = seleccionados.length > 0 ? 'block' : 'none';
        }
    }
</script>
</body>
</html>