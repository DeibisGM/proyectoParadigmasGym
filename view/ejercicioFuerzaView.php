<?php
session_start();
include_once '../business/ejercicioFuerzaBusiness.php';
include_once '../business/subZonaBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

$ejercicioFuerzaBusiness = new EjercicioFuerzaBusiness();
$subZonaBusiness = new subZonaBusiness();

if (!$esAdminOInstructor) {
    // Si no es admin/instructor, solo ejercicios activos
    $ejercicios = $ejercicioFuerzaBusiness->getTBEjercicioFuerzaByActivo();
} else {
    // Si es admin/instructor, todos los ejercicios
    $ejercicios = $ejercicioFuerzaBusiness->obtenerTbejerciciofuerza();
}

$subzonas = $subZonaBusiness->getAllTBSubZona();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ejercicios de Fuerza</title>
    <link rel="stylesheet" href="styles.css">
    <script src="../utility/Events.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2>Gestión de Ejercicios de Fuerza</h2>
    </header>

    <main>
        <?php
        // Mensajes de error y éxito
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            echo '<p class="error-message flash-msg"><b>Error: ';
            if ($error == "insertar") echo 'No se pudo guardar el ejercicio.';
            elseif ($error == "unauthorized") echo 'Acceso no autorizado.';
            elseif ($error == "dbError") echo 'Error en base de datos.';
            elseif ($error == "datos_faltantes") echo 'Complete todos los campos.';
            else echo 'Acción no válida.';
            echo '</b></p>';
        } elseif (isset($_GET['success'])) {
            $success = $_GET['success'];
            echo '<p class="success-message flash-msg"><b>Éxito: ';
            if ($success == "inserted") echo 'Ejercicio registrado correctamente.';
            elseif ($success == "updated") echo 'Ejercicio actualizado correctamente.';
            elseif ($success == "eliminado") echo 'Ejercicio eliminado.';
            echo '</b></p>';
        }
        ?>

        <?php if ($esAdminOInstructor): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Ejercicio de Fuerza</h3>
                <form method="post" action="../action/ejercicioFuerzaAction.php">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" maxlength="50"
                               value="<?= Validation::getOldInput('nombre') ?>" placeholder="Nombre del ejercicio">
                    </div>

                    <div class="form-group">
                        <label>Subzonas (seleccione una o varias):</label>
                        <span class="error-message"><?= Validation::getError('subzona') ?></span>
                        <div class="checkbox-grid">
                            <?php foreach ($subzonas as $subzona): ?>
                                <label>
                                    <input type="checkbox" name="subzona[]"
                                           value="<?= $subzona->getSubzonaid() ?>"
                                           <?= (is_array(Validation::getOldInput('subzona')) && in_array($subzona->getSubzonaid(), Validation::getOldInput('subzona'))) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($subzona->getSubzonanombre()) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <span class="error-message"><?= Validation::getError('descripcion') ?></span>
                        <textarea name="descripcion" maxlength="500" placeholder="Descripción"><?= Validation::getOldInput('descripcion') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Repeticiones:</label>
                        <span class="error-message"><?= Validation::getError('repeticion') ?></span>
                        <input type="number" name="repeticion" min="1" value="<?= Validation::getOldInput('repeticion') ?>" placeholder="Ej: 10">
                    </div>

                    <div class="form-group">
                        <label>Series:</label>
                        <span class="error-message"><?= Validation::getError('serie') ?></span>
                        <input type="number" name="serie" min="1" value="<?= Validation::getOldInput('serie') ?>" placeholder="Ej: 3">
                    </div>

                    <div class="form-group">
                        <label>Peso (requerido):</label>
                        <input type="checkbox" name="peso" value="1" <?= Validation::getOldInput('peso') == 1 ? 'checked' : '' ?>>
                    </div>

                    <div class="form-group">
                        <label>Descanso (segundos):</label>
                        <span class="error-message"><?= Validation::getError('descanso') ?></span>
                        <input type="number" name="descanso" min="0" value="<?= Validation::getOldInput('descanso') ?>" placeholder="Ej: 60">
                    </div>

                    <button type="submit" name="guardar"><i class="ph ph-floppy-disk"></i> Guardar</button>
                </form>
            </section>
        <?php endif; ?>


        <section>
            <h3><i class="ph ph-list-bullets"></i> Ejercicios Registrados</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Subzonas</th>
                        <th>Descripción</th>
                        <th>Repeticiones</th>
                        <th>Series</th>
                        <th>Peso</th>
                        <th>Descanso</th>
                        <th>Activo</th>
                        <?php if ($esAdminOInstructor): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ejercicios as $ejer): ?>
                        <?php
                        $subzonaIds = [];
                        if (!empty($ejer->getSubzonaIds())) {
                            // Corrección de sintaxis: se asume que el separador es '$' en la BD
                            $idsStr = implode('$', $ejer->getSubzonaIds());
                            $subzonaIds = array_map('intval', explode('$', $idsStr));
                        }
                        ?>
                        <tr>
                            <?php if ($esAdminOInstructor): ?>
                                <form method="post" action="../action/ejercicioFuerzaAction.php">
                                    <?php
                                    $idFila = $ejer->getId();

                                    $oldNombre = Validation::getOldInput('nombre_'.$idFila);
                                    $oldSubzona = Validation::getOldInput('subzona_'.$idFila);
                                    $oldDescripcion = Validation::getOldInput('descripcion_'.$idFila);
                                    $oldRepeticion = Validation::getOldInput('repeticion_'.$idFila);
                                    $oldSerie = Validation::getOldInput('serie_'.$idFila);
                                    $oldPeso   = Validation::getOldInput('peso_'.$idFila);
                                    $oldDescanso = Validation::getOldInput('descanso_'.$idFila);
                                    $oldActivo = Validation::getOldInput('activo_'.$idFila);

                                    $subzonaIdsMarcadas = [];
                                    if ($oldSubzona !== '' && $oldSubzona !== null) {
                                        // Corrección de sintaxis: se asume que el separador es '$' en el oldInput
                                        $subzonaIdsMarcadas = array_map('intval', explode('$', $oldSubzona));
                                    } else {
                                        $subzonaIdsMarcadas = [];
                                        if (!empty($ejer->getSubzonaIds())) {
                                            // Corrección de sintaxis: se asume que el separador es '$' en la BD
                                            $idsStr = implode('$', $ejer->getSubzonaIds());
                                            $subzonaIdsMarcadas = array_map('intval', explode('$', $idsStr));
                                        }
                                    }

                                    $valNombre = ($oldNombre !== '' && $oldNombre !== null) ? $oldNombre : $ejer->getNombre();
                                    $valDescripcion = ($oldDescripcion !== '' && $oldDescripcion !== null) ? $oldDescripcion : $ejer->getDescripcion();
                                    $valRepeticion = ($oldRepeticion !== '' && $oldRepeticion !== null) ? $oldRepeticion : $ejer->getRepeticion();
                                    $valSerie = ($oldSerie !== '' && $oldSerie !== null) ? $oldSerie : $ejer->getSerie();
                                    // Se asegura que 'peso' sea un entero (0 o 1) para el checkbox
                                    $valPeso   = ($oldPeso   !== '' && $oldPeso   !== null) ? (int)$oldPeso : (int)$ejer->getPeso();
                                    $valDescanso = ($oldDescanso !== '' && $oldDescanso !== null) ? $oldDescanso : $ejer->getDescanso();
                                    $valActivo = ($oldActivo !== '' && $oldActivo !== null) ? $oldActivo : (string)$ejer->getActivo();

                                    $errNombre = Validation::getError('nombre_'.$idFila);
                                    $errSubzona= Validation::getError('subzona_'.$idFila);
                                    $errDescripcion = Validation::getError('descripcion_'.$idFila);
                                    $errRepeticion = Validation::getError('repeticion_'.$idFila);
                                    $errSerie = Validation::getError('serie_'.$idFila);
                                    $errDescanso = Validation::getError('descanso_'.$idFila);
                                    $errActivo = Validation::getError('activo_'.$idFila);
                                    ?>
                                    <input type="hidden" name="id" value="<?= $idFila ?>">

                                    <td>
                                        <input type="text" name="nombre" maxlength="50" value="<?= htmlspecialchars($valNombre) ?>">
                                        <?php if ($errNombre): ?>
                                            <div class="error-message"><?= $errNombre ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="subzona-toggle">
                                            <button type="button" class="toggle-btn">Ver subzonas ▼</button>
                                            <div class="checkbox-grid">
                                            <?php foreach ($subzonas as $subzona): ?>
                                                <?php $sid = (int)$subzona->getSubzonaid(); ?>
                                                <label>
                                                    <input type="checkbox" name="subzona[]" value="<?= $sid ?>"
                                                            <?= in_array($sid, $subzonaIdsMarcadas, true) ? 'checked' : '' ?>>
                                                    <?= htmlspecialchars($subzona->getSubzonanombre()) ?>
                                                </label>
                                            <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php if ($errSubzona): ?>
                                            <div class="error-message"><?= $errSubzona ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <textarea name="descripcion" maxlength="500"><?= htmlspecialchars($valDescripcion) ?></textarea>
                                        <?php if ($errDescripcion): ?>
                                            <div class="error-message"><?= $errDescripcion ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <input type="number" name="repeticion" min="1" value="<?= htmlspecialchars($valRepeticion) ?>">
                                        <?php if ($errRepeticion): ?>
                                            <div class="error-message"><?= $errRepeticion ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <input type="number" name="serie" min="1" value="<?= htmlspecialchars($valSerie) ?>">
                                        <?php if ($errSerie): ?>
                                            <div class="error-message"><?= $errSerie ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <input type="checkbox" name="peso" value="1" <?= $valPeso ? 'checked' : '' ?>>
                                    </td>

                                    <td>
                                        <input type="number" name="descanso" min="0" value="<?= htmlspecialchars($valDescanso) ?>">
                                        <?php if ($errDescanso): ?>
                                            <div class="error-message"><?= $errDescanso ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <select name="activo">
                                            <option value="1" <?= ($valActivo === '1') ? 'selected' : '' ?>>Sí</option>
                                            <option value="0" <?= ($valActivo === '0') ? 'selected' : '' ?>>No</option>
                                        </select>
                                        <?php if ($errActivo): ?>
                                            <div class="error-message"><?= $errActivo ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <button type="submit" name="actualizar"><i class="ph ph-pencil"></i> Actualizar</button>
                                        <button type="submit" name="eliminar" onclick="return confirm('¿Eliminar ejercicio?');">
                                            <i class="ph ph-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </form>
                            <?php else: ?>
                                <?php if ($ejer->getActivo() == 1): ?>
                                    <td><?= htmlspecialchars($ejer->getNombre()) ?></td>
                                    <td>
                                        <div class="tag-list">
                                            <?php
                                            foreach ($subzonas as $subzona) {
                                                if (in_array((int)$subzona->getSubzonaid(), $subzonaIds, true)) {
                                                    echo '<span class="tag">' . htmlspecialchars($subzona->getSubzonanombre()) . '</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($ejer->getDescripcion()) ?></td>
                                    <td><?= htmlspecialchars($ejer->getRepeticion()) ?></td>
                                    <td><?= htmlspecialchars($ejer->getSerie()) ?></td>
                                    <td><?= $ejer->getPeso() ? 'Sí' : 'No' ?></td>
                                    <td><?= htmlspecialchars($ejer->getDescanso()) ?></td>
                                    <td>Sí</td>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date("Y") ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>

<script>
    document.querySelectorAll('.subzona-toggle').forEach(function (container) {
        const button = container.querySelector('.toggle-btn');
        const list = container.querySelector('.checkbox-grid');
        if (!button || !list) {
            return;
        }

        const hasChecked = Array.from(list.querySelectorAll('input[type="checkbox"]')).some(function (input) {
            return input.checked;
        });
        const hasError = container.querySelector('.error-message');
        if (hasChecked || hasError) {
            container.classList.add('is-open');
            button.textContent = 'Ocultar subzonas ▲';
        }

        button.addEventListener('click', function () {
            const isOpen = container.classList.toggle('is-open');
            button.textContent = isOpen ? 'Ocultar subzonas ▲' : 'Ver subzonas ▼';
        });
    });
</script>

<?php Validation::clear(); ?>
</body>
</html>