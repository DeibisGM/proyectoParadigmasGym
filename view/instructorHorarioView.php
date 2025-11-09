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

$instructores = $instructorBusiness->getAllTBInstructor(true);
$horarios = ($esInstructor) ? $instructorHorarioBusiness->getHorariosPorInstructor($usuarioId) : $instructorHorarioBusiness->getAllTBInstructorHorario($esAdmin);

$mapaInstructores = [];
foreach ($instructores as $inst) {
    $mapaInstructores[$inst->getInstructorId()] = $inst->getInstructorNombre();
}

$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios de Instructores</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Horarios de Instructores</h2>
        </header>

        <main>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message flash-msg">
                    <?php
                    $messages = [
                        'inserted' => 'Horario creado exitosamente.',
                        'updated' => 'Horario actualizado exitosamente.',
                        'deleted' => 'Horario eliminado exitosamente.'
                    ];
                    echo $messages[$_GET['success']] ?? 'Operación exitosa.';
                    ?>
                </p>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message flash-msg">
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
                </p>
            <?php endif; ?>

            <?php if ($esAdmin): ?>
                <section>
                    <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Horario</h3>
                    <form method="POST" action="../action/instructorHorarioAction.php">
                        <input type="hidden" name="create" value="1">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="instructorId">Instructor:</label>
                                <select id="instructorId" name="instructorId" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($instructores as $inst): ?>
                                        <option value="<?php echo $inst->getInstructorId(); ?>">
                                            <?php echo htmlspecialchars($inst->getInstructorNombre()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dia">Día:</label>
                                <select id="dia" name="dia" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($diasSemana as $dia): ?>
                                        <option value="<?php echo $dia; ?>">
                                            <?php echo $dia; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="horaInicio">Hora Inicio:</label>
                                <input type="time" id="horaInicio" name="horaInicio" required>
                            </div>
                            <div class="form-group">
                                <label for="horaFin">Hora Fin:</label>
                                <input type="time" id="horaFin" name="horaFin" required>
                            </div>
                        </div>
                        <button type="submit"><i class="ph ph-plus"></i> Crear Horario</button>
                    </form>
                </section>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-list-bullets"></i> Horarios Programados</h3>
                <?php if (empty($horarios)): ?>
                    <p>No hay horarios programados.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table-clients">
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
                                <?php foreach ($horarios as $horario): ?>
                                    <tr>
                                        <td data-label="Instructor">
                                            <?php echo htmlspecialchars($mapaInstructores[$horario->getInstructorId()] ?? 'N/A'); ?>
                                        </td>
                                        <td data-label="Día">
                                            <?php echo htmlspecialchars($horario->getDia()); ?>
                                        </td>
                                        <td data-label="Hora Inicio">
                                            <?php echo date('h:i A', strtotime($horario->getHoraInicio())); ?>
                                        </td>
                                        <td data-label="Hora Fin">
                                            <?php echo date('h:i A', strtotime($horario->getHoraFin())); ?>
                                        </td>
                                        <td data-label="Estado">
                                            <span
                                                class="badge-soft <?= $horario->getActivo() ? 'activo' : 'inactivo' ?>">
                                                <?= $horario->getActivo() ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <?php if ($esAdmin): ?>
                                            <td data-label="Acciones">
                                                <form method="POST" action="../action/instructorHorarioAction.php"
                                                    style="display: inline;">
                                                    <input type="hidden" name="id"
                                                        value="<?php echo $horario->getId(); ?>">
                                                    <input type="hidden" name="delete" value="1">
                                                    <button type="submit" class="btn-row btn-danger"
                                                        onclick="return confirm('¿Eliminar este horario?');">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <script>
        document.querySelector('form')?.addEventListener('submit', function (e) {
            const horaInicio = document.getElementById('horaInicio').value;
            const horaFin = document.getElementById('horaFin').value;
            if (horaInicio && horaFin && horaFin <= horaInicio) {
                e.preventDefault();
                alert('La hora de fin debe ser mayor a la hora de inicio.');
            }
        });
    </script>
</body>

</html>