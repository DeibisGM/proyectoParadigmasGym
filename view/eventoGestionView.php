<?php
include_once '../utility/Validation.php';
Validation::start();

if (!isset($_SESSION['tipo_usuario']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'instructor'])) {
    header("Location: ../view/loginView.php?error=unauthorized");
    exit();
}


$tipoUsuario = $_SESSION['tipo_usuario'];
$usuarioId = $_SESSION['usuario_id'];
$nombreUsuario = $_SESSION['usuario_nombre'];

include_once '../business/eventoBusiness.php';
include_once '../business/instructorBusiness.php';
include_once '../business/salaBusiness.php';

$eventoBusiness = new EventoBusiness();
$instructorBusiness = new InstructorBusiness();
$salaBusiness = new SalaBusiness();

$todosLosEventos = $eventoBusiness->getAllEventos();
$instructores = $instructorBusiness->getAllTBInstructor(true);
$salas = $salaBusiness->obtenerTbsala();

$misEventos = [];
if ($tipoUsuario === 'admin') {
    $misEventos = $todosLosEventos;
} else {
    foreach ($todosLosEventos as $evento) {
        if ($evento->getInstructorId() == $usuarioId) {
            $misEventos[] = $evento;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Eventos</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-calendar-plus"></i>Gestión de Eventos</h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

    <main>
        <?php if (isset($_GET['success'])): ?>
            <p class="success">¡Acción completada con éxito!</p>
        <?php endif; ?>
        <?php
            $generalError = Validation::getError('general');
            if ($generalError):
        ?>
            <p class="error">Error: <?= htmlspecialchars($generalError) ?></p>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-plus-circle"></i>Crear Nuevo Evento</h3>
            <form id="crearEventoForm" action="../action/eventoAction.php" method="POST">
                <div class="form-group">
                    <span class="error-message"><?= Validation::getError('nombre') ?></span>
                    <input type="text" name="nombre" placeholder="Nombre del Evento" value="<?= htmlspecialchars(Validation::getOldInput('nombre', '')) ?>">
                </div>
                <div class="form-group">
                    <label>Fecha del evento:</label>
                    <span class="error-message"><?= Validation::getError('fecha') ?></span>
                    <input type="date" name="fecha" value="<?= htmlspecialchars(Validation::getOldInput('fecha', date('Y-m-d'))) ?>" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>Hora de Inicio:</label>
                    <span class="error-message"><?= Validation::getError('hora_inicio') ?></span>
                    <input type="time" name="hora_inicio" value="<?= htmlspecialchars(Validation::getOldInput('hora_inicio', '')) ?>">
                </div>
                <div class="form-group">
                    <label>Hora de Fin:</label>
                    <span class="error-message"><?= Validation::getError('hora_fin') ?></span>
                    <input type="time" name="hora_fin" value="<?= htmlspecialchars(Validation::getOldInput('hora_fin', '')) ?>">
                </div>
                <div class="form-group">
                    <span class="error-message"><?= Validation::getError('aforo') ?></span>
                    <input type="number" name="aforo" placeholder="Aforo" min="1" value="<?= htmlspecialchars(Validation::getOldInput('aforo', '')) ?>">
                </div>

                <?php if ($tipoUsuario === 'admin'): ?>
                    <div class="form-group">
                        <label for="instructor_id">Asignar a instructor:</label>
                        <select name="instructor_id">
                            <option value="">Sin instructor</option>
                            <?php foreach ($instructores as $instructor): ?>
                                <option value="<?= $instructor->getInstructorId() ?>" <?= (Validation::getOldInput('instructor_id') == $instructor->getInstructorId()) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($instructor->getInstructorNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <p><strong>Instructor:</strong> <?= htmlspecialchars($nombreUsuario) ?></p>
                    <input type="hidden" name="instructor_id" value="<?= $usuarioId ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="salas">Asignar Salas (mantén presionado Ctrl para seleccionar varias):</label>
                    <span class="error-message"><?= Validation::getError('salas') ?></span>
                    <select name="salas[]" id="salas" multiple size="5">
                        <?php
                        $oldSalas = Validation::getOldInput('salas', []);
                        foreach ($salas as $sala): ?>
                            <option value="<?= $sala->getTbsalaid() ?>" <?= in_array($sala->getTbsalaid(), $oldSalas) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sala->getTbsalanombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <textarea name="descripcion" placeholder="Descripción..."><?= htmlspecialchars(Validation::getOldInput('descripcion', '')) ?></textarea>
                <button type="submit" name="crear_evento"><i class="ph ph-plus"></i>Crear Evento</button>
            </form>
        </section>
        <hr>

        <section>
            <h3>
                <i class="ph ph-list-bullets"></i><?= ($tipoUsuario === 'admin') ? 'Todos los Eventos' : 'Mis Eventos Asignados' ?>
            </h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Aforo</th>
                        <th>Instructor</th>
                        <th>Salas (No editable)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($misEventos)): ?>
                        <tr>
                            <td colspan="8">No hay eventos para mostrar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($misEventos as $evento): ?>
                             <tr>
                                <form action="../action/eventoAction.php" method="POST" id="form-evento-<?= $evento->getId() ?>" style="display: contents;">
                                    <input type="hidden" name="id" value="<?= $evento->getId() ?>">
                                    <td>
                                        <span class="error-message"><?= Validation::getError('nombre_'.$evento->getId()) ?></span>
                                        <input type="text" name="nombre" value="<?= htmlspecialchars(Validation::getOldInput('nombre', $evento->getNombre())) ?>" >
                                    </td>
                                    <td>
                                        <span class="error-message"><?= Validation::getError('fecha_'.$evento->getId()) ?></span>
                                        <input type="date" name="fecha" value="<?= htmlspecialchars(Validation::getOldInput('fecha', $evento->getFecha())) ?>" min="<?= date('Y-m-d') ?>">
                                    </td>
                                    <td>
                                        <span class="error-message"><?= Validation::getError('horaInicio_'.$evento->getId()) ?></span>
                                        <span class="error-message"><?= Validation::getError('horaFin_'.$evento->getId()) ?></span>
                                        <input type="time" name="horaInicio" value="<?= Validation::getOldInput('horaInicio', $evento->getHoraInicio()) ?>" > -
                                        <input type="time" name="horaFin" value="<?= Validation::getOldInput('horaFin', $evento->getHoraFin()) ?>" >
                                    </td>
                                    <td>
                                        <span class="error-message"><?= Validation::getError('aforo_'.$evento->getId()) ?></span>
                                        <input type="number" name="aforo" value="<?= htmlspecialchars(Validation::getOldInput('aforo', $evento->getAforo())) ?>" min="1">
                                    </td>
                                    <td>
                                        <?php if ($tipoUsuario === 'admin'): ?>
                                            <select name="instructorId">
                                                <option value="">Sin instructor</option>
                                                <?php
                                                $selectedInstructor = Validation::getOldInput('instructorId', $evento->getInstructorId());
                                                foreach ($instructores as $i): ?>
                                                    <option value="<?= $i->getInstructorId() ?>" <?= ($selectedInstructor == $i->getInstructorId() ? 'selected' : '') ?>><?= htmlspecialchars($i->getInstructorNombre()) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <span><?= htmlspecialchars($evento->getInstructorNombre() ?: 'N/A') ?></span>
                                            <input type="hidden" name="instructorId" value="<?= $evento->getInstructorId() ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <p><?= htmlspecialchars($evento->getSalasNombre()) ?></p>
                                    </td>
                                    <td>
                                        <select name="estado">
                                            <option value="1" <?= (Validation::getOldInput('estado', $evento->getEstado()) == 1) ? 'selected' : '' ?>>Activo</option>
                                            <option value="0" <?= (Validation::getOldInput('estado', $evento->getEstado()) == 0) ? 'selected' : '' ?>>Inactivo</option>
                                        </select>
                                    </td>
                                    <td class="actions-cell">
                                        <button type="submit" name="update" title="Guardar Cambios"><i class="ph ph-floppy-disk"></i></button>
                                        <?php if ($tipoUsuario === 'admin'): ?>
                                            <button type="submit" name="eliminar_evento" onclick="return confirm('¿Estás seguro? Esto eliminará el evento y TODAS las reservas de clientes asociadas.')" title="Eliminar"><i class="ph ph-trash"></i></button>
                                        <?php endif; ?>
                                    </td>
                                    <input type="hidden" name="descripcion" value="<?= htmlspecialchars($evento->getDescripcion()) ?>">
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<?php Validation::clear(); ?>
<script>
    function validarFormulario(formId) {
        const form = document.getElementById(formId);
        const fechaInput = form.querySelector('input[name="fecha"]');
        const horaInicioInput = form.querySelector('input[name="hora_inicio"], input[name="horaInicio"]');
        const horaFinInput = form.querySelector('input[name="hora_fin"], input[name="horaFin"]');
        const salasSelect = form.querySelector('select[name="salas[]"]');

        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        if (fechaInput) {
            const fechaSeleccionada = new Date(fechaInput.value + 'T00:00:00');
            if (fechaSeleccionada < hoy) {
                alert('Error: La fecha del evento no puede ser anterior a la fecha actual.');
                fechaInput.focus();
                return false;
            }
        }

        if (horaInicioInput && horaFinInput && horaInicioInput.value >= horaFinInput.value) {
            alert('Error: La hora de inicio debe ser anterior a la hora de fin.');
            horaInicioInput.focus();
            return false;
        }

        if (salasSelect && salasSelect.selectedOptions.length === 0) {
            alert('Error: Debe seleccionar al menos una sala para el evento.');
            salasSelect.focus();
            return false;
        }

        return true;
    }
</script>
</body>
</html>
