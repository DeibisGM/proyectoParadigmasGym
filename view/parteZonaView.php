<?php
session_start();
include_once '../business/parteZonaBusiness.php';
include_once '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';

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
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i> Volver al Inicio</a><br><br>
        <h2><i class="ph ph-arm-flex"></i> Partes de Zona del Cuerpo</h2>
    </header>
    <hr>

    <main>

        <!-- Crear nueva parte de zona -->
        <?php if ($esAdminOInstructor): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i> Crear Nueva Parte de Zona</h3>
                <form method="post" action="../action/parteZonaAction.php" enctype="multipart/form-data">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="text" name="descripcion" placeholder="Descripción" required>
                    <label for="zonaId">Zona del Cuerpo:</label>
                    <select name="zonaId" required>
                        <?php foreach ($zonasCuerpo as $zona): ?>
                            <option value="<?= $zona->getIdCuerpoZona() ?>">
                                <?= htmlspecialchars($zona->getNombreCuerpoZona() ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br><br>
                    <label>Imágenes:</label>
                    <input type="file" name="imagenes[]" multiple>
                    <br><br>
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
                                    <td><input type="text" name="nombre" value="<?= htmlspecialchars($parte->getPartezonanombre()) ?>"></td>
                                    <td><input type="text" name="descripcion" value="<?= htmlspecialchars($parte->getPartezonadescripcion()) ?>"></td>
                                    <td>
                                        <div class="image-gallery">
                                            <?php
                                            $imagenes = $imageManager->getImagesByIds($parte->getPartezonaimaenid());
                                            foreach ($imagenes as $img) {
                                                echo '<div class="image-container"><img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen"><button type="submit" name="borrar_imagen" value="' . $img['tbimagenid'] . '" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button></div>';
                                            }
                                            ?>
                                        </div>
                                        <label>Añadir más imágenes:</label>
                                        <input type="file" name="imagenes[]" multiple>
                                    </td>
                                    <td>
                                        <select name="activo">
                                            <option value="1" <?= $parte->getPartezonaactivo() ? 'selected' : '' ?>>Activo</option>
                                            <option value="0" <?= !$parte->getPartezonaactivo() ? 'selected' : '' ?>>Inactivo</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="actualizar"><i class="ph ph-floppy-disk"></i> Actualizar</button>
                                        <button type="submit" name="eliminar" onclick="return confirm('¿Eliminar esta parte de zona?');"><i class="ph ph-trash"></i> Eliminar</button>
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
                                                echo '<div class="image-container"><img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen"></div>';
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
</body>
</html>
