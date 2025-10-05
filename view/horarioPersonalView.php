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
$clienteId = $_SESSION['usuario_id'];

// Lógica de fechas
$timestamp = isset($_GET['semana']) ? strtotime($_GET['semana']) : time();
$diaSemanaActual = date('N', $timestamp);
$inicioSemana = (new DateTime(date('Y-m-d', $timestamp)))->modify('-' . ($diaSemanaActual - 1) . ' days');
$finSemana = (clone $inicioSemana)->modify('+6 days');
$semanaAnterior = (clone $inicioSemana)->modify('-7 days')->format('Y-m-d');
$semanaSiguiente = (clone $inicioSemana)->modify('+7 days')->format('Y-m-d');
$semanaActual = $inicioSemana->format('d/m/Y') . ' - ' . $finSemana->format('d/m/Y');

// Carga de datos
$horarioPersonalBusiness = new HorarioPersonalBusiness();

// Si es instructor, solo ve sus horarios
if ($esInstructor) {
    $horariosPersonales = $horarioPersonalBusiness->getHorariosPorInstructor($clienteId, $inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d'));
} else {
    $horariosPersonales = $horarioPersonalBusiness->getHorariosDisponibles($inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d'));
}

$instructorBusiness = new InstructorBusiness();
$instructores = $instructorBusiness->getAllTBInstructor(true);

// Mapa de instructores
$mapaInstructores = [];
foreach($instructores as $inst) {
    $mapaInstructores[$inst->getInstructorId()] = $inst->getInstructorNombre();
}

// Mapa de horarios
$mapaHorariosPersonales = [];
foreach($horariosPersonales as $hp) {
    $key = $hp->getFecha() . '_' . date('H', strtotime($hp->getHora()));
    $mapaHorariosPersonales[$key] = $hp;
}

// Mis reservas personales
$misReservasLookup = [];
if ($tipoUsuario === 'cliente') {
    $misReservas = $horarioPersonalBusiness->getMisReservasPersonales($clienteId);
    foreach ($misReservas as $reserva) {
        $misReservasLookup[$reserva->getId()] = $reserva;
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
        .grid-horario { width: 100%; border-collapse: collapse; }
        .grid-horario th, .grid-horario td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .celda-horario { height: 60px; vertical-align: top; }
        .disponible { background-color: #d4edda; cursor: pointer; }
        .ocupado { background-color: #f8d7da; }
        .reservado-por-mi { background-color: #fff3cd; }
        .deshabilitado { background-color: #f8f9fa; }
        .slot-content { font-size: 12px; }
        .btn-reservar-slot { background: #28a745; color: white; border: none; padding: 4px 8px; cursor: pointer; border-radius: 3px; }
        .btn-cancelar-slot-icon { background: #dc3545; color: white; border: none; padding: 2px 5px; cursor: pointer; border-radius: 3px; }
        .navegacion-semana { display: flex; justify-content: space-between; align-items: center; margin: 20px 0; }
        .panel-admin { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .horarios-checkbox { display: grid; grid-template-columns: repeat(6, 1fr); gap: 5px; }
    </style>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i> Volver al Inicio</a><br><br>
        <h2><i class="ph ph-user-plus"></i> Instructor Personal - Reservas</h2>
        <?php if ($esInstructor): ?>
            <p><strong>Vista de Instructor:</strong> Visualizando solo sus horarios</p>
        <?php endif; ?>
    </header>
    <hr>

    <main>
        <!-- Navegación por semana -->
        <div class="navegacion-semana">
            <a href="?semana=<?php echo $semanaAnterior; ?>"><button><i class="ph ph-caret-left"></i> Semana Anterior</button></a>
            <h3><?php echo $semanaActual; ?></h3>
            <a href="?semana=<?php echo $semanaSiguiente; ?>"><button>Semana Siguiente <i class="ph ph-caret-right"></i></button></a>
        </div>

        <?php if ($esAdmin): ?>
        <!-- Panel admin para crear horarios -->
        <div class="panel-admin">
            <h3><i class="ph ph-gear"></i> Panel Administración</h3>
            <form id="formCrearHorarios">
                <input type="hidden" name="action" value="crear_horarios">

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label><strong>Instructor:</strong></label><br>
                        <select name="instructorId" required style="width: 100%; padding: 8px;">
                            <option value="">-- Seleccionar instructor --</option>
                            <?php foreach($instructores as $inst): ?>
                                <option value="<?php echo $inst->getInstructorId(); ?>">
                                    <?php echo $inst->getInstructorNombre(); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

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
        <?php endif; ?>

        <!-- Grid de horarios -->
        <div class="grid-container">
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
                        <td class="hora-label"><?php echo str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00'; ?></td>
                        <?php
                        $fechaCelda = clone $inicioSemana;
                        for ($d = 1; $d <= 7; $d++):
                            $fechaHoraKey = $fechaCelda->format('Y-m-d') . '_' . str_pad($hora, 2, '0', STR_PAD_LEFT);
                            $dataAttr = 'data-fecha-hora="' . $fechaCelda->format('Y-m-d') . ' ' . str_pad($hora, 2, '0', STR_PAD_LEFT) . '"';

                            $clase = 'deshabilitado';
                            $contenido = '';

                            if (isset($mapaHorariosPersonales[$fechaHoraKey])) {
                                $slot = $mapaHorariosPersonales[$fechaHoraKey];
                                $dataAttr .= ' data-id="' . $slot->getId() . '"';

                                $instructorNombre = $mapaInstructores[$slot->getInstructorId()] ?? 'Instructor ' . $slot->getInstructorId();

                                if ($slot->getEstado() === 'reservado') {
                                    if ($tipoUsuario === 'cliente' && isset($misReservasLookup[$slot->getId()])) {
                                        $clase = 'reservado-por-mi';
                                        $contenido = '<div class="slot-content">';
                                        $contenido .= '<span class="slot-info"><strong>MI RESERVA</strong></span><br>';
                                        $contenido .= '<small>' . $instructorNombre . '</small><br>';
                                        $contenido .= '<button type="button" class="btn-cancelar-slot-icon" data-horario-id="' . $slot->getId() . '" title="Cancelar reserva">';
                                        $contenido .= '<i class="ph ph-x"></i> Cancelar';
                                        $contenido .= '</button>';
                                        $contenido .= '</div>';
                                    } else {
                                        $clase = 'ocupado';
                                        $clienteNombre = $slot->getClienteNombre() ?? 'Cliente ' . $slot->getClienteId();
                                        $contenido = '<div class="slot-content">';
                                        $contenido .= '<span class="slot-info"><strong>RESERVADO</strong></span><br>';
                                        $contenido .= '<small>' . $instructorNombre . '</small><br>';
                                        $contenido .= '<small>Por: ' . $clienteNombre . '</small>';
                                        $contenido .= '</div>';
                                    }
                                } else {
                                    $clase = 'disponible';
                                    $contenido = '<div class="slot-content">';
                                    $contenido .= '<span class="slot-info"><strong>DISPONIBLE</strong></span><br>';
                                    $contenido .= '<small>' . $instructorNombre . '</small><br>';
                                    if ($tipoUsuario === 'cliente') {
                                        $contenido .= '<button type="button" class="btn-reservar-slot" data-horario-id="' . $slot->getId() . '">Reservar</button>';
                                    }
                                    $contenido .= '</div>';
                                }
                            } else {
                                if ($esAdmin) {
                                    $clase = 'deshabilitado';
                                    $contenido = '<div class="slot-content"><small>No programado</small></div>';
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

        <!-- Leyenda -->
        <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
            <strong>Leyenda:</strong>
            <span style="background-color: #d4edda; padding: 2px 5px; margin: 0 5px;">Disponible</span>
            <span style="background-color: #fff3cd; padding: 2px 5px; margin: 0 5px;">Mi Reserva</span>
            <span style="background-color: #f8d7da; padding: 2px 5px; margin: 0 5px;">Ocupado</span>
            <span style="background-color: #f8f9fa; padding: 2px 5px; margin: 0 5px;">No disponible</span>
        </div>

        <?php if ($tipoUsuario === 'cliente'): ?>
            <p style="margin-top: 10px;"><strong>Instrucciones:</strong> Haz clic en "Reservar" en los horarios disponibles para agendar sesión con instructor personal.</p>
        <?php endif; ?>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de reserva
    document.querySelectorAll('.btn-reservar-slot').forEach(btn => {
        btn.addEventListener('click', function() {
            const horarioId = this.dataset.horarioId;
            reservarHorarioPersonal(horarioId);
        });
    });

    // Manejar clic en botones de cancelación
    document.querySelectorAll('.btn-cancelar-slot-icon').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const horarioId = this.dataset.horarioId;
            cancelarReservaPersonal(horarioId);
        });
    });

    // Manejar envío del formulario de admin
    const formCrearHorarios = document.getElementById('formCrearHorarios');
    if (formCrearHorarios) {
        formCrearHorarios.addEventListener('submit', function(e) {
            e.preventDefault();
            crearHorariosPersonales();
        });
    }

    function reservarHorarioPersonal(horarioId) {
        if (confirm('¿Confirmar reserva de instructor personal?')) {
            const formData = new FormData();
            formData.append('action', 'reservar_personal');
            formData.append('horarioId', horarioId);

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
            .catch(error => {
                alert('Error al procesar la reserva');
            });
        }
    }

    function cancelarReservaPersonal(horarioId) {
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
                alert(data.message);
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                alert('Error al cancelar la reserva');
            });
        }
    }

    function crearHorariosPersonales() {
        const formData = new FormData(formCrearHorarios);

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
        .catch(error => {
            alert('Error al crear horarios');
        });
    }
});
</script>
</body>
</html>