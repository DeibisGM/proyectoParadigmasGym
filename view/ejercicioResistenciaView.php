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

// Si es cliente, solo ve los activos
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
    <title>Gestión de Ejercicios de Resistencia</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .toggle-btn {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 10px;
            cursor: pointer;
            font-size: 14px;
        }
        .toggle-btn:hover {
            background-color: #0056b3;
        }
        .subzonas {
            display: none;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }
        .checkbox-group label {
            border:1px solid #ccc;
            padding:5px 10px;
            border-radius:5px;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2>Gestión de Ejercicios de Resistencia</h2>
    </header>

    <main>
        <?php
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

        <!-- Formulario de nuevo ejercicio -->
        <?php if ($esAdminOInstructor): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Ejercicio de Resistencia</h3>
                <form method="post" action="../action/ejercicioResistenciaAction.php">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" maxlength="100"
                               value="<?= Validation::getOldInput('nombre') ?>" placeholder="Nombre del ejercicio">
                    </div>

                    <div class="form-group">
                        <label>Subzonas (seleccione una o varias):</label>
                        <span class="error-message"><?= Validation::getError('subzona') ?></span>
                        <div class="checkbox-group" style="display: flex; flex-wrap: wrap; gap: 10px;">
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
                        <label>Tiempo (segundos):</label>
                        <input type="text" name="tiempo" min="1" value="<?= Validation::getOldInput('tiempo') ?>">
                    </div>

                    <div class="form-group">
                        <label>Peso:</label>
                        <input type="checkbox" name="peso" <?= Validation::getOldInput('peso') ? 'checked' : '' ?>>
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="descripcion" maxlength="255" placeholder="Descripción"><?= Validation::getOldInput('descripcion') ?></textarea>
                    </div>

                    <button type="submit" name="guardar"><i class="ph ph-floppy-disk"></i> Guardar</button>
                </form>
            </section>
        <?php endif; ?>

        <!-- Tabla de ejercicios -->
        <section>
            <h3><i class="ph ph-list-bullets"></i> Ejercicios Registrados</h3>
            <div style="overflow-x:auto;">
                <table>
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
                    <?php foreach ($ejercicios as $ejer): ?>
                        <?php
                        // Obtener lista de IDs de subzonas separadas por $
                        $subzonaIds = [];
                        if (!empty($ejer->getSubzonaIds())) {
                            // Si es algo como "26$5$7", convertirlo en array
                            $idsStr = implode('$', $ejer->getSubzonaIds());
                            $subzonaIds = array_map('intval', explode('$', $idsStr));
                        }
                        ?>
                        <tr>
                            <?php if ($esAdminOInstructor): ?>
                                <form method="post" action="../action/ejercicioResistenciaAction.php">
                                    <input type="hidden" name="id" value="<?= $ejer->getId() ?>">

                                    <td>
                                        <input type="text" name="nombre" value="<?= htmlspecialchars($ejer->getNombre()) ?>">
                                    </td>

                                    <td>
                                        <button type="button" class="toggle-btn" onclick="toggleSubzonas(this)">Ver subzonas ▼</button>
                                        <div class="checkbox-group subzonas">
                                            <?php foreach ($subzonas as $subzona): ?>
                                                <label>
                                                    <input type="checkbox" name="subzona[]"
                                                           value="<?= $subzona->getSubzonaid() ?>"
                                                            <?= in_array((int)$subzona->getSubzonaid(), $subzonaIds) ? 'checked' : '' ?>>
                                                    <?= htmlspecialchars($subzona->getSubzonanombre()) ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>

                                    <td><input type="text" name="tiempo" value="<?= $ejer->getTiempo() ?>"></td>

                                    <td><input type="checkbox" name="peso" <?= $ejer->getPeso() ? 'checked' : '' ?>></td>

                                    <td><textarea name="descripcion"><?= htmlspecialchars($ejer->getDescripcion()) ?></textarea></td>

                                    <td>
                                        <select name="activo">
                                            <option value="1" <?= $ejer->getActivo() == 1 ? 'selected' : '' ?>>Sí</option>
                                            <option value="0" <?= $ejer->getActivo() == 0 ? 'selected' : '' ?>>No</option>
                                        </select>
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
                                        <button type="button" class="toggle-btn" onclick="toggleSubzonas(this)">Ver subzonas ▼</button>
                                        <div class="subzonas">
                                            <?php
                                            $nombres = [];
                                            foreach ($subzonas as $subzona) {
                                                if (in_array((int)$subzona->getSubzonaid(), $subzonaIds)) {
                                                    $nombres[] = htmlspecialchars($subzona->getSubzonanombre());
                                                }
                                            }
                                            echo implode(', ', $nombres);
                                            ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($ejer->getTiempo()) ?></td>
                                    <td><?= $ejer->getPeso() ? 'Sí' : 'No' ?></td>
                                    <td><?= htmlspecialchars($ejer->getDescripcion()) ?></td>
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
    function toggleSubzonas(btn) {
        const div = btn.nextElementSibling;
        if (div.style.display === "none" || div.style.display === "") {
            div.style.display = "flex";
            btn.textContent = "Ocultar subzonas ▲";
        } else {
            div.style.display = "none";
            btn.textContent = "Ver subzonas ▼";
        }
    }
</script>

<?php Validation::clear(); ?>
</body>
</html>
