<?php
session_start();
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

$misEventos = ($tipoUsuario === 'admin') ? $todosLosEventos : array_filter($todosLosEventos, function ($evento) use ($usuarioId) {
    return $evento->getInstructorId() == $usuarioId;
});
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Gestión de Eventos</h2>
        </header>

        <main>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message flash-msg">¡Acción completada con éxito!</p>
            <?php endif; ?>
            <?php if ($generalError = Validation::getError('general')): ?>
                <p class="error-message flash-msg">Error:
                    <?= htmlspecialchars($generalError) ?>
                </p>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-plus-circle"></i>Crear Nuevo Evento</h3>
                <form id="crearEventoForm" action="../action/eventoAction.php" method="POST">
                    <div class="form-grid-container">
                        <div class="form-group">
                            <label for="nombre">Nombre del Evento:</label>
                            <?php if ($error = Validation::getError('nombre')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre del Evento"
                                value="<?= htmlspecialchars(Validation::getOldInput('nombre', '')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo de Evento:</label>
                            <?php if ($error = Validation::getError('tipo')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <select name="tipo" id="tipo">
                                <option value="abierto" <?= (Validation::getOldInput('tipo') == 'abierto') ? 'selected' : '' ?>>Abierto</option>
                                <option value="privado" <?= (Validation::getOldInput('tipo') == 'privado') ? 'selected' : '' ?>>Privado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fecha">Fecha:</label>
                            <?php if ($error = Validation::getError('fecha')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="date" id="fecha" name="fecha"
                                value="<?= htmlspecialchars(Validation::getOldInput('fecha', date('Y-m-d'))) ?>"
                                min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label for="hora_inicio">Hora de Inicio:</label>
                            <?php if ($error = Validation::getError('hora_inicio')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="time" id="hora_inicio" name="hora_inicio"
                                value="<?= htmlspecialchars(Validation::getOldInput('hora_inicio', '')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="hora_fin">Hora de Fin:</label>
                            <?php if ($error = Validation::getError('hora_fin')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="time" id="hora_fin" name="hora_fin"
                                value="<?= htmlspecialchars(Validation::getOldInput('hora_fin', '')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="aforo">Aforo:</label>
                            <?php if ($error = Validation::getError('aforo')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="number" id="aforo" name="aforo" placeholder="Aforo" min="1"
                                value="<?= htmlspecialchars(Validation::getOldInput('aforo', '')) ?>">
                        </div>
                        <?php if ($tipoUsuario === 'admin'): ?>
                            <div class="form-group">
                                <label for="instructor_id">Instructor:</label>
                                <select name="instructor_id" id="instructor_id">
                                    <option value="">Sin instructor</option>
                                    <?php foreach ($instructores as $instructor): ?>
                                        <option value="<?= $instructor->getInstructorId() ?>"
                                            <?= (Validation::getOldInput('instructor_id') == $instructor->getInstructorId()) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($instructor->getInstructorNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="instructor_id" value="<?= $usuarioId ?>">
                        <?php endif; ?>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="descripcion">Descripción:</label>
                            <textarea name="descripcion" id="descripcion"
                                placeholder="Descripción..."><?= htmlspecialchars(Validation::getOldInput('descripcion', '')) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Salas (mantén Ctrl para seleccionar varias):</label>
                            <?php if ($error = Validation::getError('salas')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
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
                    </div>
                    <div class="button-container">
                        <button type="submit" name="crear_evento"><i class="ph ph-plus"></i>Crear Evento</button>
                    </div>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-list-bullets"></i>
                    <?= ($tipoUsuario === 'admin') ? 'Todos los Eventos' : 'Mis Eventos' ?>
                </h3>
                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Aforo</th>
                                <th>Instructor</th>
                                <th>Salas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($misEventos)): ?>
                                <tr>
                                    <td colspan="9">No hay eventos para mostrar.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($misEventos as $evento): ?>
                                    <tr>
                                        <form id="form-<?= $evento->getId() ?>" action="../action/eventoAction.php"
                                            method="POST" style="display: contents;"></form>
                                        <input type="hidden" name="id" value="<?= $evento->getId() ?>"
                                            form="form-<?= $evento->getId() ?>">

                                        <td data-label="Nombre"><input type="text" name="nombre"
                                                value="<?= htmlspecialchars($evento->getNombre()) ?>"
                                                form="form-<?= $evento->getId() ?>"></td>
                                        <td data-label="Tipo">
                                            <select name="tipo" form="form-<?= $evento->getId() ?>">
                                                <option value="abierto" <?= ($evento->getTipo() == 'abierto') ? 'selected' : '' ?>>
                                                    Abierto</option>
                                                <option value="privado" <?= ($evento->getTipo() == 'privado') ? 'selected' : '' ?>>
                                                    Privado</option>
                                            </select>
                                        </td>
                                        <td data-label="Fecha"><input type="date" name="fecha"
                                                value="<?= htmlspecialchars($evento->getFecha()) ?>"
                                                min="<?= date('Y-m-d') ?>" form="form-<?= $evento->getId() ?>"></td>
                                        <td data-label="Horario" style="white-space: nowrap;"><input type="time"
                                                name="horaInicio" value="<?= $evento->getHoraInicio() ?>"
                                                form="form-<?= $evento->getId() ?>"> - <input type="time" name="horaFin"
                                                value="<?= $evento->getHoraFin() ?>" form="form-<?= $evento->getId() ?>">
                                        </td>
                                        <td data-label="Aforo"><input type="number" name="aforo"
                                                value="<?= htmlspecialchars($evento->getAforo()) ?>" min="1"
                                                form="form-<?= $evento->getId() ?>"></td>
                                        <td data-label="Instructor">
                                            <?php if ($tipoUsuario === 'admin'): ?>
                                                <select name="instructorId" form="form-<?= $evento->getId() ?>">
                                                    <option value="">Sin instructor</option>
                                                    <?php foreach ($instructores as $i): ?>
                                                        <option value="<?= $i->getInstructorId() ?>"
                                                            <?= ($evento->getInstructorId() == $i->getInstructorId() ? 'selected' : '') ?>>
                                                            <?= htmlspecialchars($i->getInstructorNombre()) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <span>
                                                    <?= htmlspecialchars($evento->getInstructorNombre() ?: 'N/A') ?>
                                                </span>
                                                <input type="hidden" name="instructorId"
                                                    value="<?= $evento->getInstructorId() ?>"
                                                    form="form-<?= $evento->getId() ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Salas">
                                            <?= htmlspecialchars($evento->getSalasNombre()) ?>
                                        </td>
                                        <td data-label="Estado">
                                            <select name="estado" form="form-<?= $evento->getId() ?>">
                                                <option value="1" <?= ($evento->getactivo() == 1) ? 'selected' : '' ?>>Activo
                                                </option>
                                                <option value="0" <?= ($evento->getactivo() == 0) ? 'selected' : '' ?>>
                                                    Inactivo</option>
                                            </select>
                                        </td>
                                        <td data-label="Acciones">
                                            <div class="actions">
                                                <button type="submit" name="update" class="btn-row" title="Guardar"
                                                    form="form-<?= $evento->getId() ?>"><i
                                                        class="ph ph-pencil-simple"></i></button>
                                                <?php if ($tipoUsuario === 'admin'): ?>
                                                    <button type="submit" name="eliminar_evento" class="btn-row btn-danger"
                                                        onclick="return confirm('¿Estás seguro?');" title="Eliminar"
                                                        form="form-<?= $evento->getId() ?>"><i
                                                            class="ph ph-trash"></i></button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
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
</body>

</html>