<?php
session_start();
include '../business/salaBusiness.php';
include_once '../utility/Validation.php';
Validation::start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: loginView.php");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$salaBusiness = new SalaBusiness();
$salas = $salaBusiness->obtenerTbsala();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión de Salas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2><i class="ph ph-door"></i> Gestión de Salas</h2>
        </header>

        <main>
            <?php
            if (isset($_GET['error'])) {
                echo '<p class="error-message flash-msg"><b>Error: ' . htmlspecialchars($_GET['error']) . '</b></p>';
            } else if (isset($_GET['success'])) {
                echo '<p class="success-message flash-msg"><b>Éxito: ' . htmlspecialchars($_GET['success']) . '</b></p>';
            }
            ?>

            <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') { ?>
                <section>
                    <h3><i class="ph ph-plus-circle"></i>Registrar Sala</h3>
                    <form name="salaForm" method="post" action="../action/salaAction.php">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <?php if ($error = Validation::getError('nombre')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="text" id="nombre" name="nombre" placeholder="Ej: Sala de Yoga"
                                    value="<?= Validation::getOldInput('nombre') ?>">
                            </div>
                            <div class="form-group">
                                <label for="capacidad">Capacidad:</label>
                                <?php if ($error = Validation::getError('capacidad')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="number" id="capacidad" name="capacidad" min="1" placeholder="Ej: 15"
                                    value="<?= Validation::getOldInput('capacidad') ?>">
                            </div>
                        </div>
                        <button type="submit" name="insertar"><i class="ph ph-plus"></i>Registrar Sala</button>
                    </form>
                </section>
            <?php } ?>

            <section>
                <h3><i class="ph ph-list-bullets"></i>Salas Registradas</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Capacidad</th>
                                <th>Estado</th>
                                <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor')
                                    echo '<th>Acción</th>'; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salas as $sala): ?>
                                <tr>
                                    <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor'): ?>
                                        <form id="form-<?= $sala->getTbsalaid() ?>" method="post"
                                            action="../action/salaAction.php"></form>
                                        <input type="hidden" name="id" value="<?php echo $sala->getTbsalaid(); ?>"
                                            form="form-<?= $sala->getTbsalaid() ?>">

                                        <td data-label="Nombre">
                                            <input type="text" name="nombre"
                                                value="<?php echo htmlspecialchars(Validation::getOldInput('nombre_' . $sala->getTbsalaid(), $sala->getTbsalanombre())); ?>"
                                                form="form-<?= $sala->getTbsalaid() ?>">
                                        </td>
                                        <td data-label="Capacidad">
                                            <input type="number" name="capacidad"
                                                value="<?php echo Validation::getOldInput('capacidad_' . $sala->getTbsalaid(), $sala->getTbsalacapacidad()); ?>"
                                                min="1" form="form-<?= $sala->getTbsalaid() ?>">
                                        </td>
                                        <td data-label="Estado">
                                            <select name="estado" form="form-<?= $sala->getTbsalaid() ?>">
                                                <option value="1" <?php echo (Validation::getOldInput('estado_' . $sala->getTbsalaid(), $sala->getTbsalaestado()) == 1 ? 'selected' : ''); ?>>Activa</option>
                                                <option value="0" <?php echo (Validation::getOldInput('estado_' . $sala->getTbsalaid(), $sala->getTbsalaestado()) == 0 ? 'selected' : ''); ?>>Inactiva</option>
                                            </select>
                                        </td>
                                        <td data-label="Acción">
                                            <div class="actions">
                                                <button type="submit" name="actualizar" class="btn-row" title="Actualizar"
                                                    form="form-<?= $sala->getTbsalaid() ?>"><i
                                                        class="ph ph-pencil-simple"></i></button>
                                                <?php if ($tipoUsuario == 'admin'): ?>
                                                    <button type="submit" name="eliminar" class="btn-row btn-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar?');"
                                                        form="form-<?= $sala->getTbsalaid() ?>"><i
                                                            class="ph ph-trash"></i></button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    <?php else: ?>
                                        <td data-label="Nombre">
                                            <?php echo htmlspecialchars($sala->getTbsalanombre()); ?>
                                        </td>
                                        <td data-label="Capacidad">
                                            <?php echo $sala->getTbsalacapacidad(); ?>
                                        </td>
                                        <td data-label="Estado">
                                            <?php echo ($sala->getTbsalaestado() == 1 ? 'Activa' : 'Inactiva'); ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <?php Validation::clear(); ?>
</body>

</html>