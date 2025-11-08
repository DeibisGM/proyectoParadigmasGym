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
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2><i class="ph ph-clock"></i> Gestión de Horarios</h2>
        </header>

        <main>
            <section>
                <h3>Configuración del Horario Semanal</h3>
                <p>Define los horarios de apertura, cierre y los periodos de bloqueo para cada día.</p>
                <?php if (isset($_GET['success'])): ?>
                    <p class="success-message flash-msg">¡Horario actualizado correctamente!</p>
                <?php elseif (isset($_GET['error'])): ?>
                    <p class="error-message flash-msg">Error al actualizar el horario.</p>
                <?php endif; ?>

                <form action="../action/horarioAction.php" method="post">
                    <?php foreach ($horarios as $horario): ?>
                        <div class="day-container" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1rem;">
                            <div class="day-header"
                                style="display: flex; align-items: center; gap: 1rem; font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem;">
                                <input type="checkbox" name="activo[<?= $horario->getId() ?>]" value="1"
                                    onchange="toggleDay(this, <?= $horario->getId() ?>)" <?= $horario->isActivo() ? 'checked' : '' ?>
                                    style="width: auto; height: auto;">
                                <label>
                                    <?= $horario->getDia() ?>
                                </label>
                            </div>

                            <div id="schedule-<?= $horario->getId() ?>" class="time-inputs" <?= !$horario->isActivo() ? 'style="display:none;"' : '' ?>>
                                <div class="form-grid-container">
                                    <div class="form-group">
                                        <label>Abre:</label>
                                        <select name="apertura[<?= $horario->getId() ?>]">
                                            <?php for ($h = 0; $h < 24; $h++):
                                                $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                                $selected = ($horario->getApertura() == $timeValue) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $timeValue ?>" <?= $selected ?>>
                                                    <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Cierra:</label>
                                        <select name="cierre[<?= $horario->getId() ?>]">
                                            <?php for ($h = 0; $h < 24; $h++):
                                                $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                                $selected = ($horario->getCierre() == $timeValue) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $timeValue ?>" <?= $selected ?>>
                                                    <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="bloqueos-container" style="margin-top: 1rem;">
                                    <h4><i class="ph ph-prohibit"></i> Horas Bloqueadas</h4>
                                    <div id="bloqueos-container-<?= $horario->getId() ?>">
                                        <?php foreach ($horario->getBloqueos() as $bloqueo): ?>
                                            <div class="form-grid-container" style="margin-bottom: 0.5rem; align-items: center;">
                                                <div class="form-group">
                                                    <label>Inicio:</label>
                                                    <select name="bloqueo_inicio[<?= $horario->getId() ?>][]">
                                                        <?php for ($h = 0; $h < 24; $h++):
                                                            $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                                            $selected = (isset($bloqueo['inicio']) && $bloqueo['inicio'] == $timeValue) ? 'selected' : '';
                                                            ?>
                                                            <option value="<?= $timeValue ?>" <?= $selected ?>>
                                                                <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Fin:</label>
                                                    <select name="bloqueo_fin[<?= $horario->getId() ?>][]">
                                                        <?php for ($h = 0; $h < 24; $h++):
                                                            $timeValue = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                                                            $selected = (isset($bloqueo['fin']) && $bloqueo['fin'] == $timeValue) ? 'selected' : '';
                                                            ?>
                                                            <option value="<?= $timeValue ?>" <?= $selected ?>>
                                                                <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn-row btn-danger" onclick="removeBloqueo(this)"
                                                    title="Eliminar bloqueo" style="align-self: end;"><i class="ph ph-trash"></i></button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" onclick="addBloqueo(<?= $horario->getId() ?>)"><i
                                            class="ph ph-plus"></i>Añadir Bloqueo</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" name="update_horario" style="margin-top: 1.5rem;"><i class="ph ph-floppy-disk"></i>Guardar Cambios</button>
                </form>
            </section>
        </main>
    </div>

    <script>
        function toggleDay(checkbox, dayId) {
            document.getElementById('schedule-' + dayId).style.display = checkbox.checked ? 'block' : 'none';
        }

        function addBloqueo(dayId) {
            const container = document.getElementById('bloqueos-container-' + dayId);
            let optionsHtml = '';
            for (let h = 0; h < 24; h++) {
                const hour = String(h).padStart(2, '0');
                optionsHtml += `<option value="${hour}:00:00">${hour}:00</option>`;
            }

            const newBloqueo = document.createElement('div');
            newBloqueo.className = 'form-grid-container';
            newBloqueo.style.marginBottom = '0.5rem';
            newBloqueo.style.alignItems = 'end';
            newBloqueo.innerHTML = `
                <div class="form-group">
                    <label>Inicio:</label>
                    <select name="bloqueo_inicio[${dayId}][]">${optionsHtml}</select>
                </div>
                <div class="form-group">
                    <label>Fin:</label>
                    <select name="bloqueo_fin[${dayId}][]">${optionsHtml}</select>
                </div>
                <button type="button" class="btn-row btn-danger" onclick="removeBloqueo(this)" title="Eliminar bloqueo"><i class="ph ph-trash"></i></button>
            `;
            container.appendChild(newBloqueo);
        }

        function removeBloqueo(button) {
            button.parentElement.remove();
        }
    </script>
</body>

</html>