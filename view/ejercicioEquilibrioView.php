<?php
session_start();
include_once '../business/ejercicioEquilibrioBusiness.php';
include_once '../business/subZonaBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

$ejercicioBusiness = new EjercicioEquilibrioBusiness();
$subZonaBusiness = new subZonaBusiness();

if (!$esAdminOInstructor) {
    // Si no es admin/instructor, mostrar solo ejercicios activos (si aplica)
    $ejercicios = $ejercicioBusiness->obtenerTbejercicioequilibrio();
} else {
    $ejercicios = $ejercicioBusiness->obtenerTbejercicioequilibrio();
}

$subzonas = $subZonaBusiness->getAllTBSubZona();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ejercicios de Equilibrio/Coordinación</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button">
                <i class="ph ph-arrow-left"></i>
            </a>
            <h2>
                <i class="ph ph-balance"></i>
                Gestión de Ejercicios de Equilibrio/Coordinación
            </h2>
        </header>

        <main>
            <?php
            // Mostrar errores o mensajes
            $generalError = Validation::getError('general');
            if ($generalError) {
                echo '<p class="error-message flash-msg"><b>Error: '.htmlspecialchars($generalError).'</b></p>';
            } else if (isset($_GET['error'])) {
                $error = $_GET['error'];
                echo '<p class="error-message flash-msg"><b>Error: ';
                if ($error == "datos_faltantes") echo 'Datos incompletos.';
                else if ($error == "insertar") echo 'No se pudo insertar el ejercicio.';
                else if ($error == "insertar_subzona") echo 'Ejercicio insertado pero error al guardar subzonas.';
                else if ($error == "actualizar") echo 'No se pudo actualizar el ejercicio.';
                else if ($error == "actualizar_subzona") echo 'Ejercicio actualizado pero error al guardar subzonas.';
                else if ($error == "eliminar") echo 'No se pudo eliminar el ejercicio.';
                else if ($error == "id_faltante") echo 'ID no proporcionado.';
                else if ($error == "accion_no_valida") echo 'Acción no válida.';
                else echo 'Error desconocido.';
                echo '</b></p>';
            } else if (isset($_GET['success'])) {
                $success = $_GET['success'];
                echo '<p class="success-message flash-msg"><b>Éxito: ';
                if ($success == "insertado") echo 'Ejercicio insertado correctamente.';
                else if ($success == "actualizado") echo 'Ejercicio actualizado correctamente.';
                else if ($success == "eliminado") echo 'Ejercicio eliminado correctamente.';
                echo '</b></p>';
            }
            ?>

            <!-- Formulario de inserción -->
            <?php if ($esAdminOInstructor): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i> Registrar Ejercicio de Equilibrio/Coordinación</h3>
                <form name="ejercicioForm" method="post" action="../action/ejercicioEquilibrioAction.php">
                    <div class="form-group">
                        <label>Nombre del Ejercicio:</label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" placeholder="Ej: Postura del árbol"
                               value="<?= htmlspecialchars(Validation::getOldInput('nombre')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <span class="error-message"><?= Validation::getError('descripcion') ?></span>
                        <textarea name="descripcion" placeholder="Descripción del ejercicio y cómo realizarlo" rows="3"><?= htmlspecialchars(Validation::getOldInput('descripcion')) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Subzonas (seleccione una o varias):</label>
                        <span class="error-message"><?= Validation::getError('subzona') ?></span>
                        <div class="checkbox-grid">
                            <?php foreach ($subzonas as $subzona): ?>
                                <label>
                                    <input type="checkbox" name="subzona[]"
                                           value="<?= $subzona->getSubzonaid() ?>">
                                    <?= htmlspecialchars($subzona->getSubzonanombre()) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Dificultad:</label>
                        <span class="error-message"><?= Validation::getError('dificultad') ?></span>
                        <select name="dificultad">
                            <option value="">Seleccione la dificultad</option>
                            <option value="Principiante" <?= Validation::getOldInput('dificultad') == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                            <option value="Intermedio" <?= Validation::getOldInput('dificultad') == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                            <option value="Avanzado" <?= Validation::getOldInput('dificultad') == 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Duración (segundos):</label>
                        <span class="error-message"><?= Validation::getError('duracion') ?></span>
                        <input type="number" name="duracion" placeholder="Ej: 30" min="1"
                               value="<?= htmlspecialchars(Validation::getOldInput('duracion')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Materiales (opcional):</label>
                        <input type="text" name="materiales" placeholder="Ej: Bosu, colchoneta, silla"
                               value="<?= htmlspecialchars(Validation::getOldInput('materiales')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Postura/Posición:</label>
                        <span class="error-message"><?= Validation::getError('postura') ?></span>
                        <select name="postura">
                            <option value="">Seleccione la postura</option>
                            <option value="De pie" <?= Validation::getOldInput('postura') == 'De pie' ? 'selected' : '' ?>>De pie</option>
                            <option value="Sentado" <?= Validation::getOldInput('postura') == 'Sentado' ? 'selected' : '' ?>>Sentado</option>
                            <option value="En el suelo" <?= Validation::getOldInput('postura') == 'En el suelo' ? 'selected' : '' ?>>En el suelo</option>
                            <option value="En movimiento" <?= Validation::getOldInput('postura') == 'En movimiento' ? 'selected' : '' ?>>En movimiento</option>
                        </select>
                    </div>

                    <button type="submit" name="insertar">
                        <i class="ph ph-plus"></i> Registrar Ejercicio
                    </button>
                </form>
            </section>
            <?php endif; ?>

            <!-- Tabla de ejercicios -->
            <section>
                <h3><i class="ph ph-list-bullets"></i> Ejercicios Registrados</h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Subzonas</th>
                                <th>Dificultad</th>
                                <th>Duración (seg)</th>
                                <th>Materiales</th>
                                <th>Postura</th>
                                <?php if ($esAdminOInstructor): ?>
                                    <th>Acción</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ejercicios as $ejercicio): ?>
                            <tr>
                                <?php if ($esAdminOInstructor): ?>
                                <form method="post" action="../action/ejercicioEquilibrioAction.php">
                                    <?php
                                    $idFila = $ejercicio->getTbejercicioequilibrioid();

                                    $oldNombre = Validation::getOldInput('nombre_'.$idFila);
                                    $oldDescripcion = Validation::getOldInput('descripcion_'.$idFila);
                                    $oldSubzona = Validation::getOldInput('subzona_'.$idFila);
                                    $oldDificultad = Validation::getOldInput('dificultad_'.$idFila);
                                    $oldDuracion = Validation::getOldInput('duracion_'.$idFila);
                                    $oldMateriales = Validation::getOldInput('materiales_'.$idFila);
                                    $oldPostura = Validation::getOldInput('postura_'.$idFila);

                                    $subzonaIdsMarcadas = [];
                                    if ($oldSubzona !== '' && $oldSubzona !== null) {
                                        $subzonaIdsMarcadas = array_map('intval', explode('$', $oldSubzona));
                                    } else {
                                        $subzonaIdsMarcadas = [];
                                        if (!empty($ejercicio->getSubzonaIds())) {
                                            $idsStr = implode('$', $ejercicio->getSubzonaIds());
                                            $subzonaIdsMarcadas = array_map('intval', explode('$', $idsStr));
                                        }
                                    }

                                    $valNombre = ($oldNombre !== '' && $oldNombre !== null) ? $oldNombre : $ejercicio->getTbejercicioequilibrionombre();
                                    $valDescripcion = ($oldDescripcion !== '' && $oldDescripcion !== null) ? $oldDescripcion : $ejercicio->getTbejercicioequilibriodescripcion();
                                    $valDificultad = ($oldDificultad !== '' && $oldDificultad !== null) ? $oldDificultad : $ejercicio->getTbejercicioequilibriodificultad();
                                    $valDuracion = ($oldDuracion !== '' && $oldDuracion !== null) ? $oldDuracion : $ejercicio->getTbejercicioequilibrioduracion();
                                    $valMateriales = ($oldMateriales !== '' && $oldMateriales !== null) ? $oldMateriales : $ejercicio->getTbejercicioequilibriomateriales();
                                    $valPostura = ($oldPostura !== '' && $oldPostura !== null) ? $oldPostura : $ejercicio->getTbejercicioequilibriopostura();

                                    $errNombre = Validation::getError('nombre_'.$idFila);
                                    $errDescripcion = Validation::getError('descripcion_'.$idFila);
                                    $errSubzona = Validation::getError('subzona_'.$idFila);
                                    $errDificultad = Validation::getError('dificultad_'.$idFila);
                                    $errDuracion = Validation::getError('duracion_'.$idFila);
                                    $errPostura = Validation::getError('postura_'.$idFila);
                                    ?>
                                    <input type="hidden" name="id" value="<?= $idFila ?>"/>
                                    <td>
                                        <?php if ($errNombre): ?><div class="error-message"><?= $errNombre ?></div><?php endif; ?>
                                        <input type="text" name="nombre" value="<?= htmlspecialchars($valNombre) ?>"/>
                                    </td>
                                    <td>
                                        <?php if ($errDescripcion): ?><div class="error-message"><?= $errDescripcion ?></div><?php endif; ?>
                                        <textarea name="descripcion" rows="3"><?= htmlspecialchars($valDescripcion) ?></textarea>
                                    </td>
                                    <td>
                                        <div class="subzona-toggle">
                                            <button type="button" class="toggle-btn">Ver/Editar subzonas</button>
                                            <div class="checkbox-grid">
                                                <?php foreach ($subzonas as $subzona): ?>
                                                    <label>
                                                        <input type="checkbox" name="subzona[]"
                                                               value="<?= $subzona->getSubzonaid() ?>"
                                                               <?= in_array((int)$subzona->getSubzonaid(), $subzonaIdsMarcadas, true) ? 'checked' : '' ?>>
                                                        <?= htmlspecialchars($subzona->getSubzonanombre()) ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php if ($errSubzona): ?><div class="error-message"><?= $errSubzona ?></div><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($errDificultad): ?><div class="error-message"><?= $errDificultad ?></div><?php endif; ?>
                                        <select name="dificultad">
                                            <option value="Principiante" <?= $valDificultad == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                                            <option value="Intermedio" <?= $valDificultad == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                                            <option value="Avanzado" <?= $valDificultad == 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if ($errDuracion): ?><div class="error-message"><?= $errDuracion ?></div><?php endif; ?>
                                        <input type="number" name="duracion" value="<?= htmlspecialchars($valDuracion) ?>" min="1"/>
                                    </td>
                                    <td>
                                        <input type="text" name="materiales" value="<?= htmlspecialchars($valMateriales) ?>"/>
                                    </td>
                                    <td>
                                        <?php if ($errPostura): ?><div class="error-message"><?= $errPostura ?></div><?php endif; ?>
                                        <select name="postura">
                                            <option value="De pie" <?= $valPostura == 'De pie' ? 'selected' : '' ?>>De pie</option>
                                            <option value="Sentado" <?= $valPostura == 'Sentado' ? 'selected' : '' ?>>Sentado</option>
                                            <option value="En el suelo" <?= $valPostura == 'En el suelo' ? 'selected' : '' ?>>En el suelo</option>
                                            <option value="En movimiento" <?= $valPostura == 'En movimiento' ? 'selected' : '' ?>>En movimiento</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button type="submit" name="actualizar" class="btn-primary">
                                                <i class="ph ph-check"></i>
                                            </button>
                                            <button type="submit" name="eliminar" class="btn-danger" onclick="return confirm('¿Está seguro de eliminar este ejercicio?');">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </form>
                                <?php else: ?>
                                    <td><?= htmlspecialchars($ejercicio->getTbejercicioequilibrionombre()) ?></td>
                                    <td><?= htmlspecialchars($ejercicio->getTbejercicioequilibriodescripcion()) ?></td>
                                    <td>
                                        <div class="tag-list">
                                            <?php
                                            $subzonaIds = array_map('intval', (array)$ejercicio->getSubzonaIds());
                                            foreach ($subzonas as $subzona) {
                                                if (in_array((int)$subzona->getSubzonaid(), $subzonaIds, true)) {
                                                    echo '<span class="tag">' . htmlspecialchars($subzona->getSubzonanombre()) . '</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($ejercicio->getTbejercicioequilibriodificultad()) ?></td>
                                    <td><?= htmlspecialchars($ejercicio->getTbejercicioequilibrioduracion()) ?></td>
                                    <td><?= htmlspecialchars($ejercicio->getTbejercicioequilibriomateriales()) ?></td>
                                    <td><?= htmlspecialchars($ejercicio->getTbejercicioequilibriopostura()) ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
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
            const hasError = container.parentElement.querySelector('.error-message');
            if (hasChecked || hasError) {
                container.classList.add('is-open');
                button.textContent = 'Ocultar subzonas ▲';
            }

            button.addEventListener('click', function () {
                const isOpen = container.classList.toggle('is-open');
                button.textContent = isOpen ? 'Ocultar subzonas ▲' : 'Ver/Editar subzonas';
            });
        });
    </script>
</body>
</html>