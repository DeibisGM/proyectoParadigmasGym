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
    <link rel="stylesheet" href="../styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .day-container {
            border: 1px solid #dee2e6;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }

        .day-header {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .time-inputs {
            margin-left: 20px;
        }

        .bloqueo-item {
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .bloqueos-container h4 {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-clock-clockwise"></i>Gestión de Horario del Gimnasio</h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

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
                            <input type="checkbox" name="activo[<?= $horario->getId() ?>]" value="1"
                                   onchange="toggleDay(this, <?= $horario->getId() ?>)" <?= $horario->isActivo() ? 'checked' : '' ?>>
                            <?= $horario->getDia() ?>
                        </label>
                    </div>

                    <div id="schedule-<?= $horario->getId() ?>"
                         class="time-inputs" <?= !$horario->isActivo() ? 'style="display:none;"' : '' ?>>
                        <p>
                            <label>Abre: <input type="time" name="apertura[<?= $horario->getId() ?>]"
                                                value="<?= $horario->getApertura() ?>"></label>
                            <label>Cierra: <input type="time" name="cierre[<?= $horario->getId() ?>]"
                                                  value="<?= $horario->getCierre() ?>"></label>
                        </p>
                        <div class="bloqueos-container">
                            <h4><i class="ph ph-prohibit"></i>Horas Bloqueadas</h4>
                            <div id="bloqueos-container-<?= $horario->getId() ?>">
                                <?php foreach ($horario->getBloqueos() as $index => $bloqueo): ?>
                                    <div class="bloqueo-item">
                                        <label>Inicio: <input type="time"
                                                              name="bloqueo_inicio[<?= $horario->getId() ?>][]"
                                                              value="<?= $bloqueo['inicio'] ?>"></label>
                                        <label>Fin: <input type="time" name="bloqueo_fin[<?= $horario->getId() ?>][]"
                                                           value="<?= $bloqueo['fin'] ?>"></label>
                                        <button type="button" onclick="removeBloqueo(this)" title="Eliminar bloqueo"><i class="ph ph-trash"></i> Eliminar</button>
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
        const newBloqueo = document.createElement('div');
        newBloqueo.className = 'bloqueo-item';
        newBloqueo.innerHTML = `
            <label>Inicio: <input type="time" name="bloqueo_inicio[${dayId}][]"></label>
            <label>Fin: <input type="time" name="bloqueo_fin[${dayId}][]"></label>
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