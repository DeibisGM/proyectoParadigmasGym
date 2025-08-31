<?php
session_start();
include_once '../business/horarioBusiness.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("location: ../view/loginView.php?error=unauthorized");
    exit();
}

$horarioBusiness = new HorarioBusiness();
$horarios = $horarioBusiness->getAllHorarios();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Horario del Gimnasio</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .day-container { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .day-header { font-size: 1.2em; font-weight: bold; }
        .time-inputs { margin-left: 20px; }
        .bloqueo-item { margin-bottom: 5px; }
    </style>
</head>
<body>

<header>
    <h2>Gym - Gestión de Horario</h2>
    <a href="../index.php">Volver al Inicio</a>
</header>
<hr>

<main>
    <h3>Configuración del Horario Semanal</h3>

    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;">¡Horario actualizado correctamente!</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p style="color: red;">Error al actualizar el horario.</p>
    <?php endif; ?>

    <form action="../action/horarioAction.php" method="post">

        <?php foreach ($horarios as $horario): ?>
            <div class="day-container">
                <div class="day-header">
                    <label>
                        <input type="checkbox"
                               name="activo[<?= $horario->getId() ?>]"
                               value="1"
                               onchange="toggleDay(this, <?= $horario->getId() ?>)"
                            <?= $horario->isActivo() ? 'checked' : '' ?>>
                        <?= $horario->getDia() ?>
                    </label>
                </div>

                <div id="schedule-<?= $horario->getId() ?>" class="time-inputs" <?= !$horario->isActivo() ? 'style="display:none;"' : '' ?>>
                    <p>
                        <label>Abre: <input type="time" name="apertura[<?= $horario->getId() ?>]" value="<?= $horario->getApertura() ?>"></label>
                        <label>Cierra: <input type="time" name="cierre[<?= $horario->getId() ?>]" value="<?= $horario->getCierre() ?>"></label>
                    </p>

                    <h4>Horas Bloqueadas</h4>
                    <div id="bloqueos-container-<?= $horario->getId() ?>">
                        <?php foreach ($horario->getBloqueos() as $index => $bloqueo): ?>
                            <div class="bloqueo-item">
                                <label>Inicio: <input type="time" name="bloqueo_inicio[<?= $horario->getId() ?>][]" value="<?= $bloqueo['inicio'] ?>"></label>
                                <label>Fin: <input type="time" name="bloqueo_fin[<?= $horario->getId() ?>][]" value="<?= $bloqueo['fin'] ?>"></label>
                                <button type="button" onclick="removeBloqueo(this)">Eliminar</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" onclick="addBloqueo(<?= $horario->getId() ?>)">Añadir Bloqueo</button>
                </div>
            </div>
        <?php endforeach; ?>

        <hr>
        <input type="submit" name="update_horario" value="Guardar Cambios en el Horario">
    </form>
</main>

<script>
    function toggleDay(checkbox, dayId) {
        const scheduleDiv = document.getElementById('schedule-' + dayId);
        scheduleDiv.style.display = checkbox.checked ? 'block' : 'none';
    }

    function addBloqueo(dayId) {
        const container = document.getElementById('bloqueos-container-' + dayId);
        const newBloqueo = document.createElement('div');
        newBloqueo.className = 'bloqueo-item';
        newBloqueo.innerHTML = `
            <label>Inicio: <input type="time" name="bloqueo_inicio[${dayId}][]"></label>
            <label>Fin: <input type="time" name="bloqueo_fin[${dayId}][]"></label>
            <button type="button" onclick="removeBloqueo(this)">Eliminar</button>
        `;
        container.appendChild(newBloqueo);
    }

    function removeBloqueo(button) {
        button.parentElement.remove();
    }
</script>

</body>
</html>
