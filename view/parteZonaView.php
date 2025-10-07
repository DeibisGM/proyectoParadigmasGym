<?php
session_start();
include_once '../business/parteZonaBusiness.php';
include_once '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

$parteZonaBusiness = new ParteZonaBusiness();
$cuerpoZonaBusiness = new CuerpoZonaBusiness();
$imageManager = new ImageManager();

// Obtener lista de zonas del cuerpo
$zonasCuerpo = $cuerpoZonaBusiness->getAllTBCuerpoZona();

// Filtrado
$zonaFiltro = isset($_GET['zonaFiltro']) ? intval($_GET['zonaFiltro']) : 0;

if ($zonaFiltro > 0) {
    $idsParte = $cuerpoZonaBusiness->getCuerpoZonaParteZonaId($zonaFiltro);
    if ($idsParte !== null) {
        $partesZona = $parteZonaBusiness->getAllTBParteZonaPorId($idsParte);
    } else {
        $partesZona = [];
    }
} else {
    $partesZona = $parteZonaBusiness->getAllTBParteZona();
}

if (!$esAdminOInstructor) {
    $partesZona = array_filter($partesZona, function ($parte) {
        return $parte->getPartezonaactivo() == 1;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Partes de Zona</title>
    <link rel="stylesheet" href="styles.css">
    <script src="../utility/Events.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
<header>
        <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2>Gestión de Partes de Zona</h2>
    </header>
    <hr>

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
            else if ($error == "insertar") echo 'No se pudo insertar la parte.';
            else if ($error == "dbError") echo 'Error en base de datos.';
            else if ($error == "unauthorized") echo 'Acceso no autorizado.';
            else echo 'Acción no válida.';
            echo '</b></p>';
        } else if (isset($_GET['success'])) {
            $success = $_GET['success'];
            echo '<p class="success-message flash-msg"><b>Éxito: ';
            if ($success == "inserted") echo 'Parte de zona creada correctamente.';
            else if ($success == "updated") echo 'Parte de zona actualizada correctamente.';
            else if ($success == "eliminado") echo 'Parte de zona eliminada correctamente.';
            else if ($success == "image_deleted") echo 'Imagen eliminada correctamente.';
            echo '</b></p>';
        }
        ?>

        <!-- Crear nueva parte de zona -->
        <?php if ($esAdminOInstructor): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i> Crear Nueva Parte de Zona</h3>
                <form method="post" action="../action/parteZonaAction.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" maxlength="50" placeholder="Nombre"
                               value="<?= Validation::getOldInput('nombre') ?>">
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <span class="error-message"><?= Validation::getError('descripcion') ?></span>
                        <input type="text" name="descripcion" maxlength="100" placeholder="Descripción"
                               value="<?= Validation::getOldInput('descripcion') ?>">
                    </div>
                    <div class="form-group">
                        <label>Zona del Cuerpo:</label>
                        <span class="error-message"><?= Validation::getError('zonaId') ?></span>
                        <select name="zonaId">
                            <option value="">Seleccione una zona</option>
                            <?php foreach ($zonasCuerpo as $zona): ?>
                                <option value="<?= $zona->getIdCuerpoZona() ?>"
                                        <?= Validation::getOldInput('zonaId') == $zona->getIdCuerpoZona() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($zona->getNombreCuerpoZona() ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Imágenes:</label>
                        <input type="file" name="imagenes[]" multiple>
                    </div>
                    <button type="submit" name="guardar"><i class="ph ph-plus"></i> Guardar</button>
                </form>
            </section>
        <?php endif; ?>

        <!-- Listado -->
        <section>
            <form method="get" action="">
                <label for="zonaFiltro"><i class="ph ph-funnel"></i> Filtrar por Zona:</label>
                <select name="zonaFiltro" id="zonaFiltro" onchange="this.form.submit()">
                    <option value="0">Todas</option>
                    <?php foreach ($zonasCuerpo as $zona): ?>
                        <option value="<?= $zona->getIdCuerpoZona() ?>"
                                <?= ($zonaFiltro == $zona->getIdCuerpoZona()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($zona->getNombreCuerpoZona() ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <h3><i class="ph ph-list-bullets"></i> Partes Registradas</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Imágenes</th>
                        <?php if ($esAdminOInstructor): ?>
                            <th>Activo</th>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($partesZona as $parte): ?>
                        <tr>
                            <?php if ($esAdminOInstructor): ?>
                                <form method="post" action="../action/parteZonaAction.php" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= $parte->getPartezonaid() ?>">
                                    <td>
                                        <input type="text" name="nombre" maxlength="50"
                                               value="<?= htmlspecialchars(Validation::getOldInput('nombre_'.$parte->getPartezonaid(), $parte->getPartezonanombre())) ?>">
                                        <span class="error-message"><?= Validation::getError('nombre_'.$parte->getPartezonaid()) ?></span>
                                    </td>
                                    <td>
                                        <input type="text" name="descripcion" maxlength="100"
                                               value="<?= htmlspecialchars(Validation::getOldInput('descripcion_'.$parte->getPartezonaid(), $parte->getPartezonadescripcion())) ?>">
                                    </td>
                                    <td>
                                        <div class="image-gallery">
                                            <?php
                                            $imagenes = $imageManager->getImagesByIds($parte->getPartezonaimaenid());
                                            foreach ($imagenes as $img) {
                                                echo '<div class="image-container">
                                                        <img src="..'.htmlspecialchars($img['tbimagenruta'] ?? '').'?t='.time().'" alt="Imagen">
                                                        <button type="submit" name="borrar_imagen" value="'.$img['tbimagenid'].'" 
                                                        onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button>
                                                      </div>';
                                            }
                                            ?>
                                        </div>
                                        <label>Añadir más imágenes:</label>
                                        <input type="file" name="imagenes[]" multiple>
                                    </td>
                                    <td>
                                        <select name="activo">
                                            <option value="1" <?= Validation::getOldInput('activo_'.$parte->getPartezonaid(), $parte->getPartezonaactivo()) == 1 ? 'selected' : '' ?>>Activo</option>
                                            <option value="0" <?= Validation::getOldInput('activo_'.$parte->getPartezonaid(), $parte->getPartezonaactivo()) == 0 ? 'selected' : '' ?>>Inactivo</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="actualizar"><i class="ph ph-floppy-disk"></i> Actualizar</button>
                                        <button type="submit" name="eliminar" onclick="return confirm('¿Eliminar esta parte de zona?');">
                                            <i class="ph ph-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?= htmlspecialchars($parte->getPartezonanombre()) ?></td>
                                <td><?= htmlspecialchars($parte->getPartezonadescripcion()) ?></td>
                                <td>
                                    <div class="image-gallery">
                                        <?php
                                        $imagenes = $imageManager->getImagesByIds($parte->getPartezonaimaenid());
                                        if (empty($imagenes)) {
                                            echo 'Sin imagen';
                                        } else {
                                            foreach ($imagenes as $img) {
                                                echo '<div class="image-container">
                                                        <img src="..'.htmlspecialchars($img['tbimagenruta'] ?? '').'?t='.time().'" alt="Imagen">
                                                      </div>';
                                            }
                                        }
                                        ?>
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

    <footer>
        <p>&copy; <?= date("Y") ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
<?php Validation::clear(); ?>
</body>
</html>
