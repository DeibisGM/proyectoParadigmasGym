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

$ejercicios = $ejercicioBusiness->obtenerTbejercicioequilibrio();
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
            $generalError = Validation::getError('general');
            if ($generalError) {
                echo '<p class="error-message flash-msg"><b>Error: ' . htmlspecialchars($generalError) . '</b></p>';
            } else if (isset($_GET['error'])) {
                $error = $_GET['error'];
                echo '<p class="error-message flash-msg"><b>Error: ';
                if ($error == "datos_faltantes")
                    echo 'Datos incompletos.';
                else if ($error == "insertar")
                    echo 'No se pudo insertar el ejercicio.';
                else if ($error == "actualizar")
                    echo 'No se pudo actualizar el ejercicio.';
                else if ($error == "eliminar")
                    echo 'No se pudo eliminar el ejercicio.';
                else
                    echo 'Acción no válida.';
                echo '</b></p>';
            } else if (isset($_GET['success'])) {
                $success = $_GET['success'];
                echo '<p class="success-message flash-msg"><b>Éxito: ';
                if ($success == "insertado")
                    echo 'Ejercicio insertado correctamente.';
                else if ($success == "actualizado")
                    echo 'Ejercicio actualizado correctamente.';
                else if ($success == "eliminado")
                    echo 'Ejercicio eliminado correctamente.';
                echo '</b></p>';
            }
            ?>

            <?php if ($esAdminOInstructor): ?>
                <section>
                    <h3><i class="ph ph-plus-circle"></i> Registrar Ejercicio</h3>
                    <form name="ejercicioForm" method="post" action="../action/ejercicioEquilibrioAction.php">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <?php if ($error = Validation::getError('nombre')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="text" id="nombre" name="nombre" placeholder="Ej: Postura del árbol"
                                    value="<?= htmlspecialchars(Validation::getOldInput('nombre')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <?php if ($error = Validation::getError('descripcion')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <textarea id="descripcion" name="descripcion" placeholder="Descripción del ejercicio"
                                    rows="3"><?= htmlspecialchars(Validation::getOldInput('descripcion')) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="dificultad">Dificultad:</label>
                                <?php if ($error = Validation::getError('dificultad')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <select id="dificultad" name="dificultad">
                                    <option value="">Seleccione</option>
                                    <option value="Principiante" <?= Validation::getOldInput('dificultad') == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                                    <option value="Intermedio" <?= Validation::getOldInput('dificultad') == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                                    <option value="Avanzado" <?= Validation::getOldInput('dificultad') == 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="duracion">Duración (segundos):</label>
                                <?php if ($error = Validation::getError('duracion')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="number" id="duracion" name="duracion" placeholder="Ej: 30" min="1"
                                    value="<?= htmlspecialchars(Validation::getOldInput('duracion')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="postura">Postura/Posición:</label>
                                <?php if ($error = Validation::getError('postura')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <select id="postura" name="postura">
                                    <option value="">Seleccione</option>
                                    <option value="De pie" <?= Validation::getOldInput('postura') == 'De pie' ? 'selected' : '' ?>>De pie</option>
                                    <option value="Sentado" <?= Validation::getOldInput('postura') == 'Sentado' ? 'selected' : '' ?>>Sentado</option>
                                    <option value="En el suelo" <?= Validation::getOldInput('postura') == 'En el suelo' ? 'selected' : '' ?>>En el suelo</option>
                                    <option value="En movimiento" <?= Validation::getOldInput('postura') == 'En movimiento' ? 'selected' : '' ?>>En movimiento
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="materiales">Materiales (opcional):</label>
                                <input type="text" id="materiales" name="materiales"
                                    placeholder="Ej: Bosu, colchoneta"
                                    value="<?= htmlspecialchars(Validation::getOldInput('materiales')) ?>">
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

                        <button type="submit" name="insertar"><i class="ph ph-plus"></i> Registrar Ejercicio</button>
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
                            <?php foreach ($ejercicios as $ejercicio):
                                $idFila = $ejercicio->getTbejercicioequilibrioid();
                                ?>
                                <tr>
                                    <form id="form-<?= $idFila ?>" method="post"
                                        action="../action/ejercicioEquilibrioAction.php"></form>
                                    <input type="hidden" name="id" value="<?= $idFila ?>" form="form-<?= $idFila ?>" />

                                    <td data-label="Nombre">
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="text" name="nombre"
                                                value="<?= htmlspecialchars($ejercicio->getTbejercicioequilibrionombre()) ?>"
                                                form="form-<?= $idFila ?>" />
                                        <?php else: ?>
                                            <?= htmlspecialchars($ejercicio->getTbejercicioequilibrionombre()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Descripción">
                                        <?php if ($esAdminOInstructor): ?>
                                            <textarea name="descripcion" rows="3"
                                                form="form-<?= $idFila ?>"><?= htmlspecialchars($ejercicio->getTbejercicioequilibriodescripcion()) ?></textarea>
                                        <?php else: ?>
                                            <?= htmlspecialchars($ejercicio->getTbejercicioequilibriodescripcion()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Subzonas">
                                        <?php
                                        $subzonaIds = $ejercicio->getSubzonaIds();
                                        $subzonaNombres = [];
                                        foreach ($subzonas as $subzona) {
                                            if (in_array($subzona->getSubzonaid(), $subzonaIds)) {
                                                $subzonaNombres[] = $subzona->getSubzonanombre();
                                            }
                                        }
                                        echo htmlspecialchars(implode(', ', $subzonaNombres));
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
                                    <td data-label="Dificultad">
                                        <?php if ($esAdminOInstructor): ?>
                                            <select name="dificultad" form="form-<?= $idFila ?>">
                                                <option value="Principiante" <?= $ejercicio->getTbejercicioequilibriodificultad() == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                                                <option value="Intermedio" <?= $ejercicio->getTbejercicioequilibriodificultad() == 'Intermedio' ? 'selected' : '' ?>>
                                                    Intermedio</option>
                                                <option value="Avanzado" <?= $ejercicio->getTbejercicioequilibriodificultad() == 'Avanzado' ? 'selected' : '' ?>>
                                                    Avanzado</option>
                                            </select>
                                        <?php else: ?>
                                            <?= htmlspecialchars($ejercicio->getTbejercicioequilibriodificultad()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Duración (seg)">
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="number" name="duracion"
                                                value="<?= htmlspecialchars($ejercicio->getTbejercicioequilibrioduracion()) ?>"
                                                min="1" form="form-<?= $idFila ?>" />
                                        <?php else: ?>
                                            <?= htmlspecialchars($ejercicio->getTbejercicioequilibrioduracion()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Materiales">
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="text" name="materiales"
                                                value="<?= htmlspecialchars($ejercicio->getTbejercicioequilibriomateriales()) ?>"
                                                form="form-<?= $idFila ?>" />
                                        <?php else: ?>
                                            <?= htmlspecialchars($ejercicio->getTbejercicioequilibriomateriales()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Postura">
                                        <?php if ($esAdminOInstructor): ?>
                                            <select name="postura" form="form-<?= $idFila ?>">
                                                <option value="De pie" <?= $ejercicio->getTbejercicioequilibriopostura() == 'De pie' ? 'selected' : '' ?>>De
                                                    pie</option>
                                                <option value="Sentado" <?= $ejercicio->getTbejercicioequilibriopostura() == 'Sentado' ? 'selected' : '' ?>>
                                                    Sentado</option>
                                                <option value="En el suelo" <?= $ejercicio->getTbejercicioequilibriopostura() == 'En el suelo' ? 'selected' : '' ?>>En
                                                    el suelo</option>
                                                <option value="En movimiento" <?= $ejercicio->getTbejercicioequilibriopostura() == 'En movimiento' ? 'selected' : '' ?>>En
                                                    movimiento</option>
                                            </select>
                                        <?php else: ?>
                                            <?= htmlspecialchars($ejercicio->getTbejercicioequilibriopostura()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($esAdminOInstructor): ?>
                                        <td data-label="Acción">
                                            <div class="actions">
                                                <button type="submit" name="actualizar" class="btn-row" title="Actualizar"
                                                    form="form-<?= $idFila ?>"><i
                                                        class="ph ph-pencil-simple"></i></button>
                                                <button type="submit" name="eliminar" class="btn-row btn-danger"
                                                    onclick="return confirm('¿Está seguro?');" title="Eliminar"
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
</body>

</html>
<?php
Validation::clear();
?>