<?php
session_start();
include_once '../business/subZonaBusiness.php';
include_once '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

$parteZonaBusiness = new subZonaBusiness();
$cuerpoZonaBusiness = new CuerpoZonaBusiness();
$imageManager = new ImageManager();

$zonasCuerpo = $cuerpoZonaBusiness->getAllTBCuerpoZona();
$zonaFiltro = isset($_GET['zonaFiltro']) ? intval($_GET['zonaFiltro']) : 0;

if ($zonaFiltro > 0) {
    $idsParte = $cuerpoZonaBusiness->getCuerpoZonaSubZonaId($zonaFiltro);
    $partesZona = ($idsParte !== null) ? $parteZonaBusiness->getAllTBSubZonaPorId($idsParte) : [];
} else {
    $partesZona = $parteZonaBusiness->getAllTBSubZona();
}

if (!$esAdminOInstructor) {
    $partesZona = array_filter($partesZona, fn($parte) => $parte->getSubzonaactivo() == 1);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sub Zonas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Gestión de Sub Zonas</h2>
        </header>

        <main>
            <?php
            if (isset($_GET['error'])) {
                echo '<p class="error-message flash-msg"><b>Error: ' . htmlspecialchars($_GET['error']) . '</b></p>';
            } else if (isset($_GET['success'])) {
                echo '<p class="success-message flash-msg"><b>Éxito: ' . htmlspecialchars($_GET['success']) . '</b></p>';
            }
            ?>

            <?php if ($esAdminOInstructor): ?>
                <section>
                    <h3><i class="ph ph-plus-circle"></i> Crear Nueva Sub Zona</h3>
                    <form method="post" action="../action/subzonaAction.php" enctype="multipart/form-data">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <?php if ($error = Validation::getError('nombre')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="text" id="nombre" name="nombre" maxlength="50" placeholder="Nombre"
                                    value="<?= Validation::getOldInput('nombre') ?>">
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <?php if ($error = Validation::getError('descripcion')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <input type="text" id="descripcion" name="descripcion" maxlength="100"
                                    placeholder="Descripción" value="<?= Validation::getOldInput('descripcion') ?>">
                            </div>
                            <div class="form-group">
                                <label for="zonaId">Zona del Cuerpo:</label>
                                <?php if ($error = Validation::getError('zonaId')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <select id="zonaId" name="zonaId">
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
                                <label for="imagenes" style="margin-right: 1rem;">Imágenes:</label>
                                <input type="file" id="imagenes" name="imagenes[]" multiple>
                            </div>
                        </div>
                        <div class="button-container">
                            <button type="submit" name="guardar"><i class="ph ph-plus"></i> Guardar</button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <section>
                <form method="get" action="">
                    <div class="form-group">
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
                    </div>
                </form>

                <h3 style="margin-top: 1.5rem;"><i class="ph ph-list-bullets"></i> Sub Zonas Registradas</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
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
                                    <form id="form-<?= $parte->getSubzonaid() ?>" method="post"
                                        action="../action/subzonaAction.php" enctype="multipart/form-data"></form>
                                    <input type="hidden" name="id" value="<?= $parte->getSubzonaid() ?>"
                                        form="form-<?= $parte->getSubzonaid() ?>">

                                    <td data-label="Nombre">
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="text" name="nombre" maxlength="50"
                                                value="<?= htmlspecialchars(Validation::getOldInput('nombre_' . $parte->getSubzonaid(), $parte->getSubzonanombre())) ?>"
                                                form="form-<?= $parte->getSubzonaid() ?>">
                                        <?php else: ?>
                                            <?= htmlspecialchars($parte->getSubzonanombre()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Descripción">
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="text" name="descripcion" maxlength="100"
                                                value="<?= htmlspecialchars(Validation::getOldInput('descripcion_' . $parte->getSubzonaid(), $parte->getSubzonadescripcion())) ?>"
                                                form="form-<?= $parte->getSubzonaid() ?>">
                                        <?php else: ?>
                                            <?= htmlspecialchars($parte->getSubzonadescripcion()) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Imágenes">
                                        <div
                                            style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; justify-content: center;">
                                            <?php
                                            $imagenes = $imageManager->getImagesByIds($parte->getSubzonaimaenid());
                                            if (empty($imagenes)) {
                                                echo 'Sin imagen';
                                            }
                                            foreach ($imagenes as $img) {
                                                echo '<div class="image-container" style="width: 60px; height: 60px;"><img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen" onerror="this.onerror=null; this.src=\'noimage.svg\';">';
                                                if ($esAdminOInstructor) {
                                                    echo '<button type="submit" name="delete_image" value="' . $img['tbimagenid'] . '" onclick="return confirm(\'¿Eliminar esta imagen?\');" form="form-' . $parte->getSubzonaid() . '"><i class="ph ph-x"></i></button>';
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                        <?php if ($esAdminOInstructor): ?>
                                            <input type="file" name="imagenes[]" multiple style="margin-top: 0.5rem;"
                                                form="form-<?= $parte->getSubzonaid() ?>">
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($esAdminOInstructor): ?>
                                        <td data-label="Activo">
                                            <select name="activo" form="form-<?= $parte->getSubzonaid() ?>">
                                                <option value="1"
                                                    <?= Validation::getOldInput('activo_' . $parte->getSubzonaid(), $parte->getSubzonaactivo()) == 1 ? 'selected' : '' ?>>
                                                    Activo</option>
                                                <option value="0"
                                                    <?= Validation::getOldInput('activo_' . $parte->getSubzonaid(), $parte->getSubzonaactivo()) == 0 ? 'selected' : '' ?>>
                                                    Inactivo</option>
                                            </select>
                                        </td>
                                        <td data-label="Acciones">
                                            <div class="actions">
                                                <button type="submit" name="actualizar" class="btn-row"
                                                    title="Actualizar" form="form-<?= $parte->getSubzonaid() ?>"><i
                                                        class="ph ph-pencil-simple"></i></button>
                                                <button type="submit" name="eliminar" class="btn-row btn-danger"
                                                    onclick="return confirm('¿Eliminar sub zona?');" title="Eliminar"
                                                    form="form-<?= $parte->getSubzonaid() ?>"><i
                                                        class="ph ph-trash"></i></button>
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