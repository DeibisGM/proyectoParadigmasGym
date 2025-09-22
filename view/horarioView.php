<?php
session_start();
include_once '../business/horarioBusiness.php';

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
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-clock-clockwise"></i>Gestión de Horario del Gimnasio</h2>
    </header>

    <main>
        <h3>Configuración del Horario Semanal</h3>
        <p>Seleccione únicamente horas completas. Esto asegurará la consistencia con el sistema de reservas.</p>
        <?php if (isset($_GET['success'])): ?>
            <p class="success">¡Horario actualizado correctamente!</p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error">Error al actualizar el horario.</p>
        <?php endif; ?>

        <form action="../action/horarioAction.php" method="post">
            <?php foreach ($horarios as $horario): ?>
                <div class="day-container">
                    <div class="day-header">
                        <label>
                            <input type="checkbox" name="activo[<?= $horario->getId() ?>]" value="1"
                                   onchange="toggleDay(this, <?= $horario->getId() ?>)" <?= $horario->isActivo() ? 'checked' : '' ?>>
                            <?= $horario->getDia() ?>
                        </label>
                    </div>

                    <div id="schedule-<?= $horario->getId() ?>"
                         class="time-inputs" <?= !$horario->isActivo() ? 'style="display:none;"' : '' ?>>
                        <p>
                            <label>Abre:
                                <select name="apertura[<?= $horario->getId() ?>]">
                                    <?php for ($h = 0; $h < 24; $h++):
                                        $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                        $selected = ($horario->getApertura() == $timeValue) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $timeValue ?>" <?= $selected ?>><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                                    <?php endfor; ?>
                                </select>
                            </label>
                            <label>Cierra:
                                <select name="cierre[<?= $horario->getId() ?>]">
                                    <?php for ($h = 0; $h < 24; $h++):
                                        $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                        $selected = ($horario->getCierre() == $timeValue) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $timeValue ?>" <?= $selected ?>><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                                    <?php endfor; ?>
                                </select>
                            </label>
                        </p>
                        <div class="bloqueos-container">
                            <h4><i class="ph ph-prohibit"></i>Horas Bloqueadas</h4>
                            <div id="bloqueos-container-<?= $horario->getId() ?>">
                                <?php foreach ($horario->getBloqueos() as $index => $bloqueo): ?>
                                    <div class="bloqueo-item">
                                        <label>Inicio:
                                            <select name="bloqueo_inicio[<?= $horario->getId() ?>][]">
                                                <?php for ($h = 0; $h < 24; $h++):
                                                    $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                                    $selected = (isset($bloqueo['inicio']) && $bloqueo['inicio'] == $timeValue) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?= $timeValue ?>" <?= $selected ?>><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                                                <?php endfor; ?>
                                            </select>
                                        </label>
                                        <label>Fin:
                                            <select name="bloqueo_fin[<?= $horario->getId() ?>][]">
                                                <?php for ($h = 0; $h < 24; $h++):
                                                    $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                                    $selected = (isset($bloqueo['fin']) && $bloqueo['fin'] == $timeValue) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?= $timeValue ?>" <?= $selected ?>><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                                                <?php endfor; ?>
                                            </select>
                                        </label>
                                        <button type="button" onclick="removeBloqueo(this)" title="Eliminar bloqueo"><i
                                                    class="ph ph-trash"></i> Eliminar
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" onclick="addBloqueo(<?= $horario->getId() ?>)"><i
                                        class="ph ph-plus"></i>Añadir Bloqueo
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" name="update_horario"><i class="ph ph-floppy-disk"></i>Guardar Cambios en el Horario
            </button>
        </form>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>

<script>
    function toggleDay(checkbox, dayId) {
        document.getElementById('schedule-' + dayId).style.display = checkbox.checked ? 'block' : 'none';
    }

    function addBloqueo(dayId) {
        const container = document.getElementById('bloqueos-container-' + dayId);

        // Crear las opciones para el select
        let optionsHtml = '';
        for (let h = 0; h < 24; h++) {
            const hour = String(h).padStart(2, '0');
            const timeValue = `${hour}:00:00`;
            optionsHtml += `<option value="${timeValue}">${hour}:00</option>`;
        }

        const newBloqueo = document.createElement('div');
        newBloqueo.className = 'bloqueo-item';
        newBloqueo.innerHTML = `
            <label>Inicio:
                <select name="bloqueo_inicio[${dayId}][]">${optionsHtml}</select>
            </label>
            <label>Fin:
                <select name="bloqueo_fin[${dayId}][]">${optionsHtml}</select>
            </label>
            <button type="button" onclick="removeBloqueo(this)" title="Eliminar bloqueo"><i class="ph ph-trash"></i> Eliminar</button>
        `;
        container.appendChild(newBloqueo);
    }

    function removeBloqueo(button) {
        button.parentElement.remove();
    }
</script>
</body>
</html>