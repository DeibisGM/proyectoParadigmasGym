<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: loginView.php");
    exit();
}

include_once '../business/instructorHorarioBusiness.php';
include_once '../business/instructorBusiness.php';

$tipoUsuario = $_SESSION['tipo_usuario'];
$esAdmin = ($tipoUsuario === 'admin');
$esInstructor = ($tipoUsuario === 'instructor');
$usuarioId = $_SESSION['usuario_id'];

$instructorHorarioBusiness = new InstructorHorarioBusiness();
$instructorBusiness = new InstructorBusiness();

// Obtener datos
$instructores = $instructorBusiness->getAllTBInstructor(true);

if ($esInstructor) {
    $horarios = $instructorHorarioBusiness->getHorariosPorInstructor($usuarioId);
} else {
    $horarios = $instructorHorarioBusiness->getAllTBInstructorHorario($esAdmin);
}

// Mapa de instructores para fácil acceso
$mapaInstructores = [];
foreach ($instructores as $inst) {
    $mapaInstructores[$inst->getInstructorId()] = $inst->getInstructorNombre();
}

// Días de la semana
$diasSemana = [
    'Lunes' => 'Lunes',
    'Martes' => 'Martes',
    'Miércoles' => 'Miércoles',
    'Jueves' => 'Jueves',
    'Viernes' => 'Viernes',
    'Sábado' => 'Sábado',
    'Domingo' => 'Domingo'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horarios de Instructores</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .panel-admin { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .grid-horarios { display: grid; gap: 15px; margin-top: 20px; }
        .card-horario { border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: white; }
        .card-horario.inactivo { background: #f8d7da; opacity: 0.7; }
        .horario-header { display: flex; justify-content: between; align-items: center; margin-bottom: 10px; }
        .horario-info { flex-grow: 1; }
        .horario-actions { display: flex; gap: 10px; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; }
        .form-control { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
    </style>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="container">
        <header>
            <a href="../index.php"><i class="ph ph-arrow-left"></i> Volver al Inicio</a>
            <h2><i class="ph ph-calendar"></i> Horarios de Instructores</h2>

            <?php if ($esInstructor): ?>
                <p><strong>Vista de Instructor:</strong> Visualizando solo sus horarios</p>
            <?php elseif (!$esAdmin): ?>
                <p><strong>Vista de Cliente:</strong> Visualizando horarios disponibles</p>
            <?php endif; ?>
        </header>
        <hr>

        <?php if (isset($_GET['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php
                $messages = [
                    'inserted' => 'Horario creado exitosamente.',
                    'updated' => 'Horario actualizado exitosamente.',
                    'deleted' => 'Horario eliminado exitosamente.'
                ];
                echo $messages[$_GET['success']] ?? 'Operación exitosa.';
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php
                $errors = [
                    'permission_denied' => 'No tiene permisos para realizar esta acción.',
                    'datos_faltantes' => 'Faltan datos requeridos.',
                    'horaFin_invalida' => 'La hora de fin debe ser mayor a la hora de inicio.',
                    'dbError' => 'Error en la base de datos.',
                    'id_faltante' => 'ID de horario faltante.'
                ];
                echo $errors[$_GET['error']] ?? urldecode($_GET['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($esAdmin): ?>
        <!-- Panel de administración para crear horarios -->
        <div class="panel-admin">
            <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Horario</h3>
            <form method="POST" action="../action/instructorHorarioAction.php">
                <input type="hidden" name="create" value="1">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="instructorId">Instructor:</label>
                        <select id="instructorId" name="instructorId" class="form-control" required>
                            <option value="">-- Seleccionar instructor --</option>
                            <?php foreach($instructores as $inst): ?>
                                <option value="<?php echo $inst->getInstructorId(); ?>">
                                    <?php echo $inst->getInstructorNombre() . " (" . $inst->getInstructorId() . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dia">Día:</label>
                        <select id="dia" name="dia" class="form-control" required>
                            <option value="">-- Seleccionar día --</option>
                            <?php foreach($diasSemana as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="horaInicio">Hora Inicio:</label>
                        <input type="time" id="horaInicio" name="horaInicio" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="horaFin">Hora Fin:</label>
                        <input type="time" id="horaFin" name="horaFin" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Crear Horario
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Lista de horarios -->
        <div>
            <h3><i class="ph ph-list"></i> Horarios Programados</h3>

            <?php if (empty($horarios)): ?>
                <p>No hay horarios programados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Instructor</th>
                            <th>Día</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Estado</th>
                            <?php if ($esAdmin): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($horarios as $horario): ?>
                            <tr>
                                <td>
                                    <?php
                                    $instructorNombre = $mapaInstructores[$horario->getInstructorId()] ?? 'Instructor no encontrado';
                                    echo $instructorNombre . " (" . $horario->getInstructorId() . ")";
                                    ?>
                                </td>
                                <td><?php echo $horario->getDia(); ?></td>
                                <td><?php echo date('h:i A', strtotime($horario->getHoraInicio())); ?></td>
                                <td><?php echo date('h:i A', strtotime($horario->getHoraFin())); ?></td>
                                <td>
                                    <?php if ($horario->getActivo() == 1): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($esAdmin): ?>
                                    <td>
                                        <form method="POST" action="../action/instructorHorarioAction.php" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $horario->getId(); ?>">
                                            <input type="hidden" name="delete" value="1">
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('¿Está seguro de eliminar este horario?')">
                                                <i class="ph ph-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Validación cliente-side para horas
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const horaInicio = document.getElementById('horaInicio').value;
                    const horaFin = document.getElementById('horaFin').value;

                    if (horaInicio && horaFin && horaFin <= horaInicio) {
                        e.preventDefault();
                        alert('La hora de fin debe ser mayor a la hora de inicio.');
                    }
                });
            }
        });
    </script>
</body>
</html>