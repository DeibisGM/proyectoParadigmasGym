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

// Si es cliente, obtener horarios disponibles
if ($esCliente) {
    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d', strtotime('+1 month'));
    $horariosDisponibles = $horarioPersonalBusiness->getHorariosDisponibles($fechaInicio, $fechaFin);
    $misReservas = $horarioPersonalBusiness->getMisReservasPersonales($usuarioId);
}

// Si es admin o instructor, mantener la lógica original para crear horarios
if ($esAdmin || $esInstructor) {
    $timestamp = isset($_GET['semana']) ? strtotime($_GET['semana']) : time();
    $diaSemanaActual = date('N', $timestamp);
    $inicioSemana = (new DateTime(date('Y-m-d', $timestamp)))->modify('-' . ($diaSemanaActual - 1) . ' days');
    $finSemana = (clone $inicioSemana)->modify('+6 days');
    $semanaAnterior = (clone $inicioSemana)->modify('-7 days')->format('Y-m-d');
    $semanaSiguiente = (clone $inicioSemana)->modify('+7 days')->format('Y-m-d');
    $semanaActual = $inicioSemana->format('d/m/Y') . ' - ' . $finSemana->format('d/m/Y');

    if ($esInstructor) {
        $horariosPersonales = $horarioPersonalBusiness->getHorariosPorInstructor($usuarioId, $inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d'));
    } else {
        $horariosPersonales = $horarioPersonalBusiness->getHorariosDisponibles($inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d'));
    }

    $instructores = $instructorBusiness->getAllTBInstructor(true);

    // Crear mapa de horarios para el calendario - ESTO ES LO QUE FALTABA
    $mapaHorariosPersonales = [];
    $mapaInstructores = [];

    foreach ($horariosPersonales as $horario) {
        $fecha = $horario->getFecha();
        $hora = substr($horario->getHora(), 0, 2); // Extraer solo la hora (08, 09, etc.)
        $key = $fecha . '_' . $hora;
        $mapaHorariosPersonales[$key] = $horario;

        // También mapear nombres de instructores
        if (!isset($mapaInstructores[$horario->getInstructorId()])) {
            $mapaInstructores[$horario->getInstructorId()] = $horario->getInstructorNombre();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instructor Personal - Reservas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-horarios { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table-horarios th, .table-horarios td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .table-horarios th { background-color: #f8f9fa; font-weight: bold; }
        .btn-reservar { background: #28a745; color: white; border: none; padding: 8px 16px; cursor: pointer; border-radius: 4px; }
        .btn-cancelar { background: #dc3545; color: white; border: none; padding: 8px 16px; cursor: pointer; border-radius: 4px; }
        .disponible { background-color: #d4edda; }
        .reservado { background-color: #fff3cd; }
        .ocupado { background-color: #f8d7da; }
        .panel-admin { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .horarios-checkbox { display: grid; grid-template-columns: repeat(6, 1fr); gap: 5px; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php">← Volver al Inicio</a><br><br>
        <h2>Instructor Personal - Reservas</h2>
        <?php if ($esInstructor): ?>
            <p><strong>Vista de Instructor:</strong> Visualizando solo sus horarios</p>
        <?php endif; ?>
    </header>
    <hr>

    <main>
        <?php if ($esCliente): ?>
            <!-- VISTA SIMPLIFICADA PARA CLIENTES -->
            <h3>Horarios Disponibles para Reservar</h3>

            <?php if (!empty($misReservas)): ?>
            <div style="margin-bottom: 30px;">
                <h4>Mis Reservas Activas</h4>
                <table class="table-horarios">
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
                        <tr class="reservado">
                            <td><?php echo date('d/m/Y', strtotime($reserva->getFecha())); ?></td>
                            <td><?php echo substr($reserva->getHora(), 0, 5); ?></td>
                            <td><?php echo htmlspecialchars($reserva->getInstructorNombre()); ?></td>
                            <td><?php echo $reserva->getDuracion(); ?> minutos</td>
                            <td>
                                <button class="btn-cancelar" onclick="cancelarReserva(<?php echo $reserva->getId(); ?>)">
                                    Cancelar Reserva
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <h4>Horarios Disponibles</h4>
            <?php if (empty($horariosDisponibles)): ?>
                <p>No hay horarios disponibles para reservar en este momento.</p>
            <?php else: ?>
                <table class="table-horarios">
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
                        <tr class="<?php echo $horario->getEstado() === 'disponible' ? 'disponible' : 'ocupado'; ?>">
                            <td><?php echo date('d/m/Y', strtotime($horario->getFecha())); ?></td>
                            <td><?php echo substr($horario->getHora(), 0, 5); ?></td>
                            <td><?php echo htmlspecialchars($horario->getInstructorNombre()); ?></td>
                            <td><?php echo $horario->getDuracion(); ?> minutos</td>
                            <td>
                                <?php
                                if ($horario->getEstado() === 'disponible') {
                                    echo '🟢 Disponible';
                                } else {
                                    echo '🔴 Ocupado';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($horario->getEstado() === 'disponible'): ?>
                                <button class="btn-reservar" onclick="reservarHorario(<?php echo $horario->getId(); ?>)">
                                    Reservar
                                </button>
                                <?php else: ?>
                                    <span style="color: #6c757d;">No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php else: ?>
            <!-- VISTA ORIGINAL PARA ADMIN E INSTRUCTOR (CALENDARIO) -->
            <!-- Navegación por semana -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
                <a href="?semana=<?php echo $semanaAnterior; ?>"><button>← Semana Anterior</button></a>
                <h3><?php echo $semanaActual; ?></h3>
                <a href="?semana=<?php echo $semanaSiguiente; ?>"><button>Semana Siguiente →</button></a>
            </div>

            <!-- Panel admin/instructor para crear horarios -->
            <div class="panel-admin">
                <h3>Panel <?php echo $esAdmin ? 'Administración' : 'Instructor'; ?></h3>
                <form id="formCrearHorarios">
                    <input type="hidden" name="action" value="crear_horarios">

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <?php if ($esAdmin): ?>
                        <div>
                            <label><strong>Instructor:</strong></label><br>
                            <select name="instructorId" required style="width: 100%; padding: 8px;">
                                <option value="">-- Seleccionar instructor --</option>
                                <?php foreach($instructores as $inst): ?>
                                    <option value="<?php echo $inst->getInstructorId(); ?>">
                                        <?php echo $inst->getInstructorNombre() . " (ID: " . $inst->getInstructorId() . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="instructorId" value="<?php echo $usuarioId; ?>">
                        <?php endif; ?>

                        <div>
                            <label><strong>Fecha:</strong></label><br>
                            <input type="date" name="fecha" required value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 8px;">
                        </div>

                        <div>
                            <label><strong>Duración (minutos):</strong></label><br>
                            <input type="number" name="duracion" value="60" min="30" max="120" style="width: 100%; padding: 8px;">
                        </div>
                    </div>

                    <div>
                        <label><strong>Horarios a crear:</strong></label><br>
                        <div class="horarios-checkbox">
                            <?php for ($hora = 8; $hora < 20; $hora++): ?>
                                <label style="display: block; margin: 5px 0;">
                                    <input type="checkbox" name="horarios[]" value="<?php echo str_pad($hora, 2, '0', STR_PAD_LEFT); ?>:00">
                                    <?php echo str_pad($hora, 2, '0', STR_PAD_LEFT); ?>:00
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px;">
                        Crear Horarios Personales
                    </button>
                </form>
            </div>

            <!-- Grid de horarios (solo para admin/instructor) -->
            <div class="grid-container">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Hora</th>
                        <?php
                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                        $fechaTemp = clone $inicioSemana;
                        foreach ($dias as $dia) {
                            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $dia . '<br><small>' . $fechaTemp->format('d/m') . '</small></th>';
                            $fechaTemp->modify('+1 day');
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($hora = 8; $hora < 20; $hora++): ?>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?php echo str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00'; ?></td>
                            <?php
                            $fechaCelda = clone $inicioSemana;
                            for ($d = 1; $d <= 7; $d++):
                                $fechaFormateada = $fechaCelda->format('Y-m-d');
                                $horaFormateada = str_pad($hora, 2, '0', STR_PAD_LEFT);
                                $fechaHoraKey = $fechaFormateada . '_' . $horaFormateada;

                                $clase = 'deshabilitado';
                                $contenido = '';

                                // Buscar si existe un horario en esta fecha y hora
                                if (isset($mapaHorariosPersonales[$fechaHoraKey])) {
                                    $slot = $mapaHorariosPersonales[$fechaHoraKey];
                                    $instructorNombre = $slot->getInstructorNombre() ?? 'Instructor ' . $slot->getInstructorId();

                                    if ($slot->getEstado() === 'reservado') {
                                        $clase = 'ocupado';
                                        $clienteNombre = $slot->getClienteNombre() ?? 'Cliente ' . $slot->getClienteId();
                                        $contenido = '<div style="font-size: 12px;">';
                                        $contenido .= '<strong>RESERVADO</strong><br>';
                                        $contenido .= '<small>' . $instructorNombre . '</small><br>';
                                        $contenido .= '<small>Por: ' . $clienteNombre . '</small>';
                                        $contenido .= '</div>';
                                    } else {
                                        $clase = 'disponible';
                                        $contenido = '<div style="font-size: 12px;">';
                                        $contenido .= '<strong>DISPONIBLE</strong><br>';
                                        $contenido .= '<small>' . $instructorNombre . '</small>';
                                        $contenido .= '</div>';
                                    }
                                } else {
                                    $clase = 'deshabilitado';
                                    $contenido = '<div style="font-size: 12px;"><small>No programado</small></div>';
                                }

                                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center; height: 60px; vertical-align: top; background-color: ' .
                                    ($clase === 'disponible' ? '#d4edda' : ($clase === 'ocupado' ? '#f8d7da' : '#f8f9fa')) . '">' . $contenido . '</td>';
                                $fechaCelda->modify('+1 day');
                            endfor;
                            ?>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
// Funciones para reservar y cancelar
function reservarHorario(horarioId) {
    console.log("Reservando horario ID:", horarioId);

    if (!horarioId || horarioId === 0 || horarioId === '0' || horarioId === 'undefined') {
        alert('Error: ID de horario no válido');
        return;
    }

    horarioId = parseInt(horarioId);

    if (isNaN(horarioId) || horarioId <= 0) {
        alert('Error: ID de horario no válido');
        return;
    }

    if (confirm('¿Confirmar reserva de instructor personal?')) {
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Reservando...';
        button.disabled = true;

        const formData = new FormData();
        formData.append('action', 'reservar_personal');
        formData.append('horarioId', horarioId);

        fetch('../action/horarioPersonalAction.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert('❌ ' + data.message);
                button.textContent = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la reserva');
            button.textContent = originalText;
            button.disabled = false;
        });
    }
}

function cancelarReserva(horarioId) {
    if (confirm('¿Cancelar reserva de instructor personal?')) {
        const formData = new FormData();
        formData.append('action', 'cancelar_personal');
        formData.append('horarioId', horarioId);

        fetch('../action/horarioPersonalAction.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cancelar la reserva');
        });
    }
}

// Solo para admin/instructor: manejar creación de horarios
<?php if ($esAdmin || $esInstructor): ?>
document.addEventListener('DOMContentLoaded', function() {
    const formCrearHorarios = document.getElementById('formCrearHorarios');
    if (formCrearHorarios) {
        formCrearHorarios.addEventListener('submit', function(e) {
            e.preventDefault();
            crearHorariosPersonales();
        });
    }

    function crearHorariosPersonales() {
        const form = document.getElementById('formCrearHorarios');
        const formData = new FormData(form);

        fetch('../action/horarioPersonalAction.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al crear horarios');
        });
    }
});
<?php endif; ?>
</script>
</body>
</html>