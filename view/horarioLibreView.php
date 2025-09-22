<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: loginView.php");
    exit();
}

include_once '../business/horarioBusiness.php';
include_once '../business/horarioLibreBusiness.php';
include_once '../business/instructorBusiness.php';

$tipoUsuario = $_SESSION['tipo_usuario'];
$esAdmin = ($tipoUsuario === 'admin');

// --- Lógica de Fechas para la Semana ---
$timestamp = isset($_GET['semana']) ? strtotime($_GET['semana']) : time();
$diaSemanaActual = date('N', $timestamp);
$inicioSemana = (clone (new DateTime(date('Y-m-d', $timestamp))))->modify('-' . ($diaSemanaActual - 1) . ' days');
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
if ($horaMinima >= $horaMaxima) {
    $horaMinima = 8;
    $horaMaxima = 18;
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
        .grid-horario th, .grid-horario td { border: 1px solid #ddd; padding: 4px; text-align: center; min-height: 50px; }
        .grid-horario th { background-color: #f2f2f2; }
        .hora-label { font-weight: bold; vertical-align: middle; }
        .celda-horario { vertical-align: top; font-size: 0.75em; position: relative; line-height: 1.3; }
        .celda-horario.deshabilitado { background-color: #e9ecef; color: #adb5bd; cursor: not-allowed; }
        .celda-horario.disponible { background-color: #fff; }
        .celda-horario.seleccionado { background-color: #b3d7ff !important; border: 2px solid #007bff; }
        .celda-horario.creado { background-color: #d4edda; }
        .celda-horario.creado strong { font-size: 1.1em; }
        .celda-horario.creado.disponible-cliente { cursor: pointer; }
        .celda-horario.creado.disponible-cliente:hover { background-color: #c3e6cb; border: 2px solid #155724; }
        .celda-horario.creado.lleno { background-color: #f8d7da; color: #721c24; cursor: not-allowed; }
        .navegacion-semana { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px; }
        .btn-eliminar-slot, .btn-seleccionar-slot { position: absolute; top: 2px; right: 2px; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center; line-height: 20px; }
        .btn-eliminar-slot { background: #dc3545; color: white; }
        .btn-seleccionar-slot { background: #007bff; color: white; }
        .celda-horario.seleccionado .btn-seleccionar-slot { background-color: #28a745; }
        #panel-admin { border: 1px solid #ddd; padding: 15px; margin-top: 20px; border-radius: 8px; background-color: #f8f9fa; }
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

        <div id="mensaje" style="display:none;"></div>

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
                                $clase = 'disponible';
                                $fechaHoraKey = $fechaCelda->format('Y-m-d') . '_' . str_pad($hora, 2, '0', STR_PAD_LEFT);
                                $dataAttr = 'data-fecha-hora="' . $fechaCelda->format('Y-m-d') . ' ' . str_pad($hora, 2, '0', STR_PAD_LEFT) . '"';

                                if (isset($mapaHorariosLibres[$fechaHoraKey])) {
                                    $slot = $mapaHorariosLibres[$fechaHoraKey];
                                    $instructorNombre = $mapaInstructores[$slot->getInstructorId()] ?? 'N/A';
                                    $disponibles = $slot->getCupos() - $slot->getMatriculados();
                                    $clase = 'creado';

                                    if ($disponibles <= 0) $clase .= ' lleno';
                                    elseif ($tipoUsuario == 'cliente') $clase .= ' disponible-cliente';

                                    $contenido = "<strong>{$instructorNombre}</strong><br>Cupos: {$disponibles}/{$slot->getCupos()}";
                                    if ($esAdmin) $contenido .= '<button class="btn-eliminar-slot" data-id="' . $slot->getId() . '">X</button>';
                                    $dataAttr .= ' data-id="' . $slot->getId() . '"';
                                } else {
                                    if ($esAdmin) $contenido = '<button class="btn-seleccionar-slot" title="Seleccionar/Deseleccionar">+</button>';
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
            <div id="panel-admin">
                <p>Haga clic en el botón <button class="btn-seleccionar-slot" style="position:relative; top:0; right:0;">+</button> de cada celda para añadirla a la selección.</p>
                <button id="btn-crear-slots" style="display: none;"><i class="ph ph-plus-circle"></i> Crear Espacios para (<span id="contador-seleccion">0</span>) Celdas</button>
                <button id="btn-limpiar-seleccion" style="display: none; background-color: #ffc107;">Limpiar Selección</button>
            </div>
        <?php else: ?>
            <p style="margin-top: 20px;"><strong>Instrucciones:</strong> Haz clic en un espacio verde disponible para realizar tu reserva.</p>
        <?php endif; ?>

    </main>
</div>

<?php if ($esAdmin): ?>
    <div id="modal-crear" class="modal">
        <div class="modal-content">
            <h3>Crear Nuevos Espacios de Uso Libre</h3>
            <p>Se crearán <strong id="num-seleccionados"></strong> espacios.</p>
            <form id="form-crear-slots">
                <label for="instructorId">Seleccionar Instructor:</label>
                <select id="instructorId" name="instructorId" required>
                    <option value="">-- Elige un instructor --</option>
                    <?php foreach($instructores as $instructor): ?>
                        <option value="<?php echo $instructor->getInstructorId(); ?>"><?php echo htmlspecialchars($instructor->getInstructorNombre()); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="cupos">Cantidad de Cupos por Hora:</label>
                <input type="number" id="cupos" name="cupos" min="1" required>
                <button type="submit">Confirmar Creación</button>
                <button type="button" onclick="document.getElementById('modal-crear').style.display='none'">Cancelar</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const esAdmin = <?php echo json_encode($esAdmin); ?>;
        const tipoUsuario = <?php echo json_encode($tipoUsuario); ?>;

        if (esAdmin) setupAdminInteractions();
        if (tipoUsuario === 'cliente') setupClientInteractions();
    });

    function setupAdminInteractions() {
        const grid = document.querySelector('.grid-horario');
        const btnCrear = document.getElementById('btn-crear-slots');
        const btnLimpiar = document.getElementById('btn-limpiar-seleccion');
        const contadorSeleccion = document.getElementById('contador-seleccion');

        grid.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-eliminar-slot')) {
                e.stopPropagation();
                const slotId = e.target.dataset.id;
                if (confirm('¿Seguro que quieres eliminar este espacio? Se borrarán también las reservas de los clientes asociadas.')) {
                    eliminarSlot(slotId);
                }
                return;
            }

            if (e.target.classList.contains('btn-seleccionar-slot')) {
                const cell = e.target.closest('td');
                cell.classList.toggle('seleccionado');
                e.target.textContent = cell.classList.contains('seleccionado') ? '✓' : '+';
                updateAdminPanel();
            }
        });

        btnCrear.addEventListener('click', function() {
            const seleccionados = document.querySelectorAll('.celda-horario.seleccionado');
            document.getElementById('num-seleccionados').textContent = seleccionados.length;
            document.getElementById('modal-crear').style.display = 'block';
        });

        btnLimpiar.addEventListener('click', function() {
            document.querySelectorAll('.celda-horario.seleccionado').forEach(cell => {
                cell.classList.remove('seleccionado');
                const btn = cell.querySelector('.btn-seleccionar-slot');
                if (btn) btn.textContent = '+';
            });
            updateAdminPanel();
        });

        document.getElementById('form-crear-slots').addEventListener('submit', function(e) {
            e.preventDefault();
            const seleccionados = document.querySelectorAll('.celda-horario.seleccionado');
            const slots = Array.from(seleccionados).map(c => c.dataset.fechaHora);
            const instructorId = document.getElementById('instructorId').value;
            const cupos = document.getElementById('cupos').value;

            if (!instructorId || !cupos || cupos < 1) {
                alert("Por favor, selecciona un instructor y define una cantidad de cupos válida.");
                return;
            }

            const formData = new FormData();
            formData.append('accion', 'crear');
            slots.forEach(s => formData.append('slots[]', s));
            formData.append('instructorId', instructorId);
            formData.append('cupos', cupos);

            fetch('../action/horarioLibreAction.php', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                mostrarMensaje(data.message, data.success);
                if(data.success) setTimeout(() => location.reload(), 1500);
            }).catch(err => mostrarMensaje("Error de conexión.", false));

            document.getElementById('modal-crear').style.display = 'none';
        });

        function updateAdminPanel() {
            const count = document.querySelectorAll('.celda-horario.seleccionado').length;
            contadorSeleccion.textContent = count;
            btnCrear.style.display = count > 0 ? 'inline-block' : 'none';
            btnLimpiar.style.display = count > 0 ? 'inline-block' : 'none';
        }

        function eliminarSlot(id) {
            const formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('id', id);

            fetch('../action/horarioLibreAction.php', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                mostrarMensaje(data.message, data.success);
                if(data.success) setTimeout(() => location.reload(), 1500);
            }).catch(err => mostrarMensaje("Error de conexión.", false));
        }
    }

    function setupClientInteractions() {
        document.querySelector('.grid-horario').addEventListener('click', function(e) {
            const cell = e.target.closest('.celda-horario.disponible-cliente');
            if (!cell) return;

            const horarioLibreId = cell.dataset.id;
            const [fecha, hora] = cell.dataset.fechaHora.split(' ');

            if (confirm(`¿Desea reservar un espacio para el ${fecha} a las ${hora}:00?`)) {
                const formData = new FormData();
                formData.append('action', 'create');
                formData.append('horarioLibreId', horarioLibreId);

                fetch('../action/reservaAction.php', { method: 'POST', body: formData })
                    .then(res => res.json()).then(data => {
                    mostrarMensaje(data.message, data.success);
                    if(data.success) setTimeout(() => location.reload(), 1500);
                }).catch(err => mostrarMensaje("Error de conexión.", false));
            }
        });
    }

    function mostrarMensaje(mensaje, esExito) {
        const div = document.getElementById('mensaje');
        div.textContent = mensaje;
        div.className = esExito ? 'success' : 'error';
        div.style.display = 'block';
        setTimeout(() => {
            div.style.display = 'none';
            div.className = '';
        }, 5000);
    }
</script>
</body>
</html>