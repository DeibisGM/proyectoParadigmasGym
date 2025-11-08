<?php
session_start();
include_once '../business/ejercicioResistenciaBusiness.php';
include_once '../business/subZonaBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

$ejercicioResistenciaBusiness = new ejercicioResistenciaBusiness();
$subZonaBusiness = new subZonaBusiness();

if (!$esAdminOInstructor) {
    $ejercicios = $ejercicioResistenciaBusiness->getTBEjercicioResisteciaByActivo();
} else {
    $ejercicios = $ejercicioResistenciaBusiness->getAllTBEjercicioResistecia();
}

$subzonas = $subZonaBusiness->getAllTBSubZona();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ejercicios de Resistencia</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2><i class="ph ph-timer"></i> Gestión de Ejercicios de Resistencia</h2>
        </header>

        <main>
            <?php
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                echo '<p class="error-message flash-msg"><b>Error: ';
                if ($error == "insertar")
                    echo 'No se pudo guardar el ejercicio.';
                elseif ($error == "dbError")
                    echo 'Error en base de datos.';
                elseif ($error == "datos_faltantes")
                    echo 'Complete todos los campos.';
                else
                    echo 'Acción no válida.';
                echo '</b></p>';
            } elseif (isset($_GET['success'])) {
                $success = $_GET['success'];
                echo '<p class="success-message flash-msg"><b>Éxito: ';
                if ($success == "inserted")
                    echo 'Ejercicio registrado correctamente.';
                elseif ($success == "updated")
                    echo 'Ejercicio actualizado correctamente.';
                elseif ($success == "eliminado")
                    echo 'Ejercicio eliminado.';
                echo '</b></p>';
            }
            ?>

            <?php if ($esAdminOInstructor): ?>
                <section>
                    <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Ejercicio</h3>
                    <form method="post" action="../action/ejercicioResistenciaAction.php">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <?php if ($error = Validation::getError('nombre')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="text" id="nombre" name="nombre" maxlength="50"
                                    value="<?= Validation::getOldInput('nombre') ?>" placeholder="Nombre del ejercicio">
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <?php if ($error = Validation::getError('descripcion')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <textarea id="descripcion" name="descripcion" maxlength="500"
                                    placeholder="Descripción"><?= Validation::getOldInput('descripcion') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="tiempo">Tiempo (segundos):</label>
                                <?php if ($error = Validation::getError('tiempo')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="number" id="tiempo" name="tiempo"
                                    value="<?= Validation::getOldInput('tiempo') ?>" min="1" placeholder="Ej: 60">
                            </div>
                            <div class="form-group form-group-horizontal" style="align-items: center;">
                                <label for="peso">Peso (requerido):</label>
                                <input type="checkbox" id="peso" name="peso" value="1" <?= Validation::getOldInput('peso') ? 'checked' : '' ?>
                                    style="width: auto; height: auto;">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 1rem;">
                            <label>Subzonas (seleccione una o varias):</label>
                            <?php if ($error = Validation::getError('subzona')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                                <?php foreach ($subzonas as $subzona): ?>
                                    <label
                                        style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal;">
                                        <input type="checkbox" name="subzona[]" value="<?= $subzona->getSubzonaid() ?>">
                                        <?= htmlspecialchars($subzona->getSubzonanombre()) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" name="guardar"><i class="ph ph-floppy-disk"></i> Guardar</button>
                    </form>
                </section>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-list-bullets"></i> Ejercicios Registrados</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Subzonas</th>
                                <th>Tiempo</th>
                                <th>Peso</th>
                                <th>Descripción</th>
                                <th>Activo</th>
                                <?php if ($esAdminOInstructor): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ejercicios as $ejer):
                                $idFila = $ejer->getId();
                                ?>
                                <tr>
                                    <form id="form-<?= $idFila ?>" method="post"
                                        action="../action/ejercicioResistenciaAction.php"></form>
                                    <input type="hidden" name="id" value="<?= $idFila ?>" form="form-<?= $idFila ?>">

                                    <td data-label="Nombre">
                                        <input type="text" name="nombre"
                                            value="<?= htmlspecialchars($ejer->getNombre()) ?>"
                                            <?= $esAdminOInstructor ? '' : 'readonly' ?> form="form-<?= $idFila ?>">
                                    </td>
                                    <td data-label="Subzonas">
                                        <?php
                                        $subzonaIds = [];
                                        if (!empty($ejer->getSubzonaIds())) {
                                            $idsStr = implode('$', $ejer->getSubzonaIds());
                                            $subzonaIds = array_map('intval', explode('$', $idsStr));
                                        }
                                        $nombres = [];
                                        foreach ($subzonas as $subzona) {
                                            if (in_array((int) $subzona->getSubzonaid(), $subzonaIds)) {
                                                $nombres[] = htmlspecialchars($subzona->getSubzonanombre());
                                            }
                                        }
                                        echo implode(', ', $nombres);
                                        ?>
                                        <?php if ($esAdminOInstructor): ?>
                                            <details style="margin-top: 0.5rem;">
                                                <summary>Editar</summary>
                                                <div
                                                    style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem; position: absolute; background: var(--color-surface-strong); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--color-border); z-index: 10;">
                                                    <?php foreach ($subzonas as $subzona): ?>
                                                        <label>
                                                            <input type="checkbox" name="subzona[]"
                                                                value="<?= $subzona->getSubzonaid() ?>"
                                                                <?= in_array($subzona->getSubzonaid(), $subzonaIds) ? 'checked' : '' ?>
                                                                form="form-<?= $idFila ?>">
                                                            <?= htmlspecialchars($subzona->getSubzonanombre()) ?>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </details>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Tiempo">
                                        <input type="number" name="tiempo"
                                            value="<?= htmlspecialchars($ejer->getTiempo()) ?>" min="1"
                                            <?= $esAdminOInstructor ? '' : 'readonly' ?> form="form-<?= $idFila ?>">
                                    </td>
                                    <td data-label="Peso">
                                        <input type="checkbox" name="peso" value="1" <?= $ejer->getPeso() ? 'checked' : '' ?>
                                            <?= $esAdminOInstructor ? '' : 'disabled' ?>
                                            style="width: auto; height: auto; display: block; margin: auto;"
                                            form="form-<?= $idFila ?>">
                                    </td>
                                    <td data-label="Descripción">
                                        <textarea name="descripcion"
                                            <?= $esAdminOInstructor ? '' : 'readonly' ?>
                                            form="form-<?= $idFila ?>"><?= htmlspecialchars($ejer->getDescripcion()) ?></textarea>
                                    </td>
                                    <td data-label="Activo">
                                        <?php if ($esAdminOInstructor): ?>
                                            <select name="activo" form="form-<?= $idFila ?>">
                                                <option value="1" <?= ($ejer->getActivo() == 1) ? 'selected' : '' ?>>Sí
                                                </option>
                                                <option value="0" <?= ($ejer->getActivo() == 0) ? 'selected' : '' ?>>No
                                                </option>
                                            </select>
                                        <?php else: ?>
                                            <?= ($ejer->getActivo() == 1) ? 'Sí' : 'No' ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($esAdminOInstructor): ?>
                                        <td data-label="Acciones">
                                            <div class="actions">
                                                <button type="submit" name="actualizar" class="btn-row" title="Actualizar"
                                                    form="form-<?= $idFila ?>"><i
                                                        class="ph ph-pencil-simple"></i></button>
                                                <button type="submit" name="eliminar" class="btn-row btn-danger"
                                                    onclick="return confirm('¿Eliminar ejercicio?');" title="Eliminar"
                                                    form="form-<?= $idFila ?>"><i class="ph ph-trash"></i></button>
                                            </div>
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