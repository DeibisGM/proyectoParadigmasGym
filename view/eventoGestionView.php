<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'instructor'])) {
    header("Location: ../view/loginView.php?error=unauthorized");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$usuarioId = $_SESSION['usuario_id'];
$nombreUsuario = $_SESSION['usuario_nombre'];

include_once '../business/eventoBusiness.php';
include_once '../business/instructorBusiness.php';

$eventoBusiness = new EventoBusiness();
$instructorBusiness = new InstructorBusiness();

$todosLosEventos = $eventoBusiness->getAllEventos();
$instructores = $instructorBusiness->getAllTBInstructor(true);

$misEventos = [];
$otrosEventos = [];

if ($tipoUsuario === 'instructor') {
    foreach ($todosLosEventos as $evento) {
        if ($evento->getInstructorId() == $usuarioId) {
            $misEventos[] = $evento;
        } else {
            $otrosEventos[] = $evento;
        }
    }
} else { // Admin
    $misEventos = $todosLosEventos;
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
        <?php if (isset($_GET['success'])) : ?>
            <p style="color: green;">¡Acción completada con éxito!</p>
        <?php elseif (isset($_GET['error'])) : ?>
            <p style="color: red;">Error: <?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-plus-circle"></i>Crear Nuevo Evento</h3>
            <form action="../action/eventoAction.php" method="POST">
                <input type="text" name="nombre" placeholder="Nombre del Evento" required>
                <label>Fecha del evento:</label>
                <input type="date" name="fecha" required>
                <input type="time" name="hora_inicio" required>
                <input type="time" name="hora_fin" required>
                <input type="number" name="aforo" placeholder="Aforo" required>
                
                <?php if ($tipoUsuario === 'admin') : ?>
                    <label for="instructor_id">Asignar a instructor:</label>
                    <select name="instructor_id">
                        <option value="">Sin instructor</option>
                        <?php foreach ($instructores as $instructor) echo "<option value='{$instructor->getInstructorId()}'>" . htmlspecialchars($instructor->getInstructorNombre()) . "</option>"; ?>
                    </select>
                <?php else : ?>
                    <p><strong>Instructor:</strong> <?= htmlspecialchars($nombreUsuario) ?></p>
                    <input type="hidden" name="instructor_id" value="<?= $usuarioId ?>">
                <?php endif; ?>

                <textarea name="descripcion" placeholder="Descripción..."></textarea>
                <button type="submit" name="crear_evento"><i class="ph ph-plus"></i>Crear Evento</button>
            </form>
        </section>
        <hr>

        <section>
            <h3><i class="ph ph-list-bullets"></i><?= ($tipoUsuario === 'admin') ? 'Todos los Eventos' : 'Mis Eventos Asignados' ?></h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Aforo</th>
                        <th>Instructor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($misEventos)) : ?>
                        <tr><td colspan="7">No hay eventos para mostrar.</td></tr>
                    <?php else : ?>
                        <?php foreach ($misEventos as $evento) : ?>
                            <tr>
                                <form action="../action/eventoAction.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $evento->getId() ?>">
                                    <td><input type="text" name="nombre" value="<?= htmlspecialchars($evento->getNombre()) ?>" placeholder="Nombre del Evento"></td>
                                    <td><input type="date" name="fecha" value="<?= htmlspecialchars($evento->getFecha()) ?>" required></td>
                                    <td>
                                        <input type="time" name="horaInicio" value="<?= $evento->getHoraInicio() ?>"> -
                                        <input type="time" name="horaFin" value="<?= $evento->getHoraFin() ?>">
                                    </td>
                                    <td><input type="number" name="aforo" value="<?= $evento->getAforo() ?>" placeholder="Aforo"></td>
                                    <td>
                                        <?php if ($tipoUsuario === 'admin') : ?>
                                            <select name="instructorId">
                                                <option value="">Sin instructor</option>
                                                <?php foreach ($instructores as $i) echo "<option value='{$i->getInstructorId()}' " . ($evento->getInstructorId() == $i->getInstructorId() ? 'selected' : '') . ">" . htmlspecialchars($i->getInstructorNombre()) . "</option>"; ?>
                                            </select>
                                        <?php else : ?>
                                            <span><?= htmlspecialchars($evento->getInstructorNombre() ?: 'N/A') ?></span>
                                            <input type="hidden" name="instructorId" value="<?= $evento->getInstructorId() ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="estado">
                                            <option value="1" <?= $evento->getEstado() == 1 ? 'selected' : '' ?>>Activo</option>
                                            <option value="0" <?= $evento->getEstado() == 0 ? 'selected' : '' ?>>Inactivo</option>
                                        </select>
                                    </td>
                                    <td class="actions-cell">
                                        <button type="submit" name="update" title="Guardar"><i class="ph ph-floppy-disk"></i></button>
                                        <?php if ($tipoUsuario === 'admin') : ?>
                                            <button type="submit" name="eliminar_evento" onclick="return confirm('¿Estás seguro de eliminar este evento?')" title="Eliminar"><i class="ph ph-trash"></i></button>
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

        <?php if ($tipoUsuario === 'instructor' && !empty($otrosEventos)) : ?>
        <hr>
        <section>
            <h3><i class="ph ph-users"></i>Otros Eventos</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Aforo</th>
                            <th>Instructor</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($otrosEventos as $evento) : ?>
                            <tr>
                                <td><?= htmlspecialchars($evento->getNombre()) ?></td>
                                <td><?= htmlspecialchars($evento->getFecha()) ?></td>
                                <td><?= $evento->getHoraInicio() ?> - <?= $evento->getHoraFin() ?></td>
                                <td><?= $evento->getAforo() ?></td>
                                <td><?= htmlspecialchars($evento->getInstructorNombre() ?: 'N/A') ?></td>
                                <td><?= $evento->getEstado() == 1 ? 'Activo' : 'Inactivo' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>
