<?php
session_start();
include_once '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');
$imageManager = new ImageManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Zonas del Cuerpo</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

</head>
<body>

<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-person-simple-run"></i>Zonas del Cuerpo</h2>

    </header>
    <hr>
    <main>
        <?php if ($esAdminOInstructor): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Crear Nueva Zona</h3>
                <form method="post" action="../action/cuerpoZonaAction.php" enctype="multipart/form-data">
                    <input type="text" name="tbcuerpozonanombre" placeholder="Nombre (Ej: Pecho)" required>
                    <input type="text" name="tbcuerpozonadescripcion" placeholder="Descripción" required>
                    <label>Imágenes (puede seleccionar varias):</label><br><br>
                    <input type="file" name="imagenes[]" accept="image/png, image/jpeg, image/webp" multiple>
                    <br><br>
                    <button type="submit" name="create"><i class="ph ph-plus"></i>Crear Zona</button>
                </form>
            </section>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-list-bullets"></i>Zonas Registradas</h3>
            <?php
            $cuerpoZonaBusiness = new CuerpoZonaBusiness();
            $allCuerpoZonas = $esAdminOInstructor ?
                    $cuerpoZonaBusiness->getAllTBCuerpoZona() :
                    $cuerpoZonaBusiness->getActiveTBCuerpoZona();
            ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Imágenes</th>
                        <?php if ($esAdminOInstructor): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allCuerpoZonas as $current): ?>
                        <tr>
                            <?php if ($esAdminOInstructor): ?>
                                <form method="post" action="../action/cuerpoZonaAction.php"
                                      enctype="multipart/form-data">
                                    <input type="hidden" name="tbcuerpozonaid"
                                           value="<?= $current->getIdCuerpoZona() ?>">
                                    <td><input type="text" name="tbcuerpozonanombre"
                                               value="<?= htmlspecialchars($current->getNombreCuerpoZona() ?? '') ?>"
                                               placeholder="Nombre">
                                    </td>
                                    <td><input type="text" name="tbcuerpozonadescripcion"
                                               value="<?= htmlspecialchars($current->getDescripcionCuerpoZona() ?? '') ?>"
                                               placeholder="Descripción">
                                    </td>
                                    <td>
                                        <div class="image-gallery">
                                            <?php
                                            $imagenes = $imageManager->getImagesByIds($current->getImagenesIds());
                                            foreach ($imagenes as $img) {
                                                echo '<div class="image-container"><img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen"><button type="submit" name="delete_image" value="' . $img['tbimagenid'] . '" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button></div>';
                                            }
                                            ?>
                                        </div>
                                        <label>Añadir más imágenes:</label>
                                        <input type="file" name="imagenes[]" multiple>
                                    </td>
                                    <td class="actions-cell">
                                        <input type="hidden" name="tbcuerpozonaactivo"
                                               value="<?= $current->getActivoCuerpoZona() ?>">
                                        <button type="submit" name="update" title="Actualizar"><i
                                                    class="ph ph-floppy-disk"></i> Actualizar
                                        </button>
                                        <button type="submit" name="delete"
                                                onclick="return confirm('¿Eliminar esta zona y todas sus imágenes?');"
                                                title="Eliminar Zona"><i class="ph ph-trash"></i> Eliminar
                                        </button>
                                        <?php if ($current->getActivoCuerpoZona()): ?>
                                            <button type="submit" name="desactivar" title="Desactivar"><i
                                                        class="ph ph-toggle-right"></i> Desactivar
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="activar" title="Activar"><i
                                                        class="ph ph-toggle-left"></i> Activar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </form>
                            <?php else: // Vista Cliente ?>
                                <td><?= htmlspecialchars($current->getNombreCuerpoZona() ?? '') ?></td>
                                <td><?= htmlspecialchars($current->getDescripcionCuerpoZona() ?? '') ?></td>
                                <td>
                                    <div class="image-gallery">
                                        <?php
                                        $imagenes = $imageManager->getImagesByIds($current->getImagenesIds());
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
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>