<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'instructor'])) {
    header("Location: ../view/loginView.php?error=unauthorized");
    exit();
}

$form_data = [];
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
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
$salas = $salaBusiness->getAllSalas();

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
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error">Error: <?= htmlspecialchars(urldecode($_GET['error'])) ?></p>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-plus-circle"></i>Crear Nuevo Evento</h3>
            <form id="crearEventoForm" action="../action/eventoAction.php" method="POST"
                  onsubmit="return validarFormulario('crearEventoForm')">
                <input type="text" name="nombre" placeholder="Nombre del Evento" required
                       value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>">
                <label>Fecha del evento:</label>
                <input type="date" name="fecha" required
                       value="<?= htmlspecialchars($form_data['fecha'] ?? date('Y-m-d')) ?>" min="<?= date('Y-m-d') ?>">
                <label>Hora de Inicio:</label>
                <input type="time" name="hora_inicio" required
                       value="<?= htmlspecialchars($form_data['hora_inicio'] ?? '') ?>">
                <label>Hora de Fin:</label>
                <input type="time" name="hora_fin" required
                       value="<?= htmlspecialchars($form_data['hora_fin'] ?? '') ?>">
                <input type="number" name="aforo" placeholder="Aforo" required min="1"
                       value="<?= htmlspecialchars($form_data['aforo'] ?? '') ?>">

                <?php if ($tipoUsuario === 'admin'): ?>
                    <label for="instructor_id">Asignar a instructor:</label>
                    <select name="instructor_id">
                        <option value="">Sin instructor</option>
                        <?php foreach ($instructores as $instructor): ?>
                            <option value="<?= $instructor->getInstructorId() ?>" <?= (isset($form_data['instructor_id']) && $form_data['instructor_id'] == $instructor->getInstructorId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($instructor->getInstructorNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p><strong>Instructor:</strong> <?= htmlspecialchars($nombreUsuario) ?></p>
                    <input type="hidden" name="instructor_id" value="<?= $usuarioId ?>">
                <?php endif; ?>

                <label for="salas">Asignar Salas (mantén presionado Ctrl para seleccionar varias):</label>
                <select name="salas[]" id="salas" multiple required size="5">
                    <?php foreach ($salas as $sala): ?>
                        <option value="<?= $sala->getTbsalaid() ?>" <?= (isset($form_data['salas']) && in_array($sala->getTbsalaid(), $form_data['salas'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sala->getTbsalanombre()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <textarea name="descripcion"
                          placeholder="Descripción..."><?= htmlspecialchars($form_data['descripcion'] ?? '') ?></textarea>
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
                            <form action="../action/eventoAction.php" method="POST"
                                  id="form-evento-<?= $evento->getId() ?>"
                                  onsubmit="return validarFormulario('form-evento-<?= $evento->getId() ?>')">
                                <tr>
                                    <input type="hidden" name="id" value="<?= $evento->getId() ?>">
                                    <td><input type="text" name="nombre"
                                               value="<?= htmlspecialchars($evento->getNombre()) ?>" required></td>
                                    <td><input type="date" name="fecha"
                                               value="<?= htmlspecialchars($evento->getFecha()) ?>" required
                                               min="<?= date('Y-m-d') ?>"></td>
                                    <td>
                                        <input type="time" name="horaInicio" value="<?= $evento->getHoraInicio() ?>"
                                               required> -
                                        <input type="time" name="horaFin" value="<?= $evento->getHoraFin() ?>" required>
                                    </td>
                                    <td><input type="number" name="aforo" value="<?= $evento->getAforo() ?>" required
                                               min="1"></td>
                                    <td>
                                        <?php if ($tipoUsuario === 'admin'): ?>
                                            <select name="instructorId">
                                                <option value="">Sin instructor</option>
                                                <?php foreach ($instructores as $i) echo "<option value='{$i->getInstructorId()}' " . ($evento->getInstructorId() == $i->getInstructorId() ? 'selected' : '') . ">" . htmlspecialchars($i->getInstructorNombre()) . "</option>"; ?>
                                            </select>
                                        <?php else: ?>
                                            <span><?= htmlspecialchars($evento->getInstructorNombre() ?: 'N/A') ?></span>
                                            <input type="hidden" name="instructorId"
                                                   value="<?= $evento->getInstructorId() ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <p><?= htmlspecialchars($evento->getSalasNombre()) ?></p>
                                    </td>
                                    <td>
                                        <select name="estado">
                                            <option value="1" <?= $evento->getEstado() == 1 ? 'selected' : '' ?>>
                                                Activo
                                            </option>
                                            <option value="0" <?= $evento->getEstado() == 0 ? 'selected' : '' ?>>
                                                Inactivo
                                            </option>
                                        </select>
                                    </td>
                                    <td class="actions-cell">
                                        <button type="submit" name="update" title="Guardar Cambios"><i
                                                    class="ph ph-floppy-disk"></i></button>
                                        <?php if ($tipoUsuario === 'admin'): ?>
                                            <button type="submit" name="eliminar_evento"
                                                    onclick="return confirm('¿Estás seguro? Esto eliminará el evento y TODAS las reservas de clientes asociadas.')"
                                                    title="Eliminar"><i class="ph ph-trash"></i></button>
                                        <?php endif; ?>
                                    </td>
                                    <input type="hidden" name="descripcion"
                                           value="<?= htmlspecialchars($evento->getDescripcion()) ?>">
                                </tr>
                            </form>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
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