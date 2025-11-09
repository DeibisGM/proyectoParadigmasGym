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
$cuerpoZonaBusiness = new CuerpoZonaBusiness();
$allCuerpoZonas = $esAdminOInstructor ?
    $cuerpoZonaBusiness->getAllTBCuerpoZona() :
    $cuerpoZonaBusiness->getActiveTBCuerpoZona();

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
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Gestión de Zonas del Cuerpo</h2>
        </header>

        <main>
            <?php if ($esAdminOInstructor): ?>
                <section>
                    <h3><i class="ph ph-plus-circle"></i>Crear Nueva Zona</h3>
                    <form method="post" action="../action/cuerpoZonaAction.php" enctype="multipart/form-data">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" id="nombre" name="tbcuerpozonanombre" placeholder="Ej: Pecho"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <input type="text" id="descripcion" name="tbcuerpozonadescripcion"
                                    placeholder="Descripción de la zona" required>
                            </div>
                            <div class="form-group">
                                <label for="imagenes" style="margin-right: 1rem;">Imágenes:</label>
                                <input type="file" id="imagenes" name="imagenes[]"
                                    accept="image/png, image/jpeg, image/webp" multiple>
                            </div>
                        </div>
                        <div class="button-container">
                            <button type="submit" name="create"><i class="ph ph-plus"></i>Crear Zona</button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-list-bullets"></i>Zonas Registradas</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
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
                                    <form id="form-<?= $current->getIdCuerpoZona() ?>" method="post"
                                        action="../action/cuerpoZonaAction.php" enctype="multipart/form-data"></form>
                                    <input type="hidden" name="tbcuerpozonaid"
                                        value="<?= $current->getIdCuerpoZona() ?>"
                                        form="form-<?= $current->getIdCuerpoZona() ?>">

                                    <td data-label="Nombre">
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="text" name="tbcuerpozonanombre"
                                                value="<?= htmlspecialchars($current->getNombreCuerpoZona() ?? '') ?>"
                                                form="form-<?= $current->getIdCuerpoZona() ?>">
                                        <?php else: ?>
                                            <?= htmlspecialchars($current->getNombreCuerpoZona() ?? '') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Descripción">
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="text" name="tbcuerpozonadescripcion"
                                                value="<?= htmlspecialchars($current->getDescripcionCuerpoZona() ?? '') ?>"
                                                form="form-<?= $current->getIdCuerpoZona() ?>">
                                        <?php else: ?>
                                            <?= htmlspecialchars($current->getDescripcionCuerpoZona() ?? '') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Imágenes">
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                                            <?php
                                            $imagenes = $imageManager->getImagesByIds($current->getImagenesIds());
                                            if (empty($imagenes)) {
                                                echo 'Sin imagen';
                                            }
                                            foreach ($imagenes as $img) {
                                                echo '<div class="image-container" style="width: 60px; height: 60px;"><img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen" onerror="this.onerror=null; this.src=\'noimage.svg\';">';
                                                if ($esAdminOInstructor) {
                                                    echo '<button type="submit" name="delete_image" value="' . $img['tbimagenid'] . '" onclick="return confirm(\'¿Eliminar esta imagen?\');" form="form-' . $current->getIdCuerpoZona() . '"><i class="ph ph-x"></i></button>';
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="file" name="imagenes[]" multiple style="margin-top: 0.5rem;"
                                                form="form-<?= $current->getIdCuerpoZona() ?>">
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($esAdminOInstructor): ?>
                                        <td data-label="Acciones">
                                            <div class="actions">
                                                <button type="submit" name="update"
                                                    form="form-<?= $current->getIdCuerpoZona() ?>" class="btn-row"
                                                    title="Actualizar"><i class="ph ph-pencil-simple"></i></button>
                                                <button type="submit" name="delete"
                                                    onclick="return confirm('¿Eliminar esta zona?');"
                                                    form="form-<?= $current->getIdCuerpoZona() ?>"
                                                    class="btn-row btn-danger" title="Eliminar"><i
                                                        class="ph ph-trash"></i></button>
                                                <?php if ($current->getActivoCuerpoZona()): ?>
                                                    <button type="submit" name="desactivar"
                                                        form="form-<?= $current->getIdCuerpoZona() ?>" class="btn-row"
                                                        title="Desactivar"><i class="ph ph-toggle-right"></i></button>
                                                <?php else: ?>
                                                    <button type="submit" name="activar"
                                                        form="form-<?= $current->getIdCuerpoZona() ?>" class="btn-row"
                                                        title="Activar"><i class="ph ph-toggle-left"></i></button>
                                                <?php endif; ?>
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