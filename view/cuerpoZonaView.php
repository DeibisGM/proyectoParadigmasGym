<?php
session_start();
include '../business/cuerpoZonaBusiness.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

// Verificar el tipo de usuario
$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Zonas del Cuerpo</title>
    <style>
        table {
            table-layout: fixed;
            width: 100%;
        }

        td, th {
            overflow: hidden;
            word-wrap: break-word;
        }

        input[type="text"] {
            width: 95%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<header>
    <h2>Gym - Zonas del Cuerpo</h2>
    <a href="../index.php">Volver al Inicio</a>
</header>

<hr>

<main>
    <?php if ($esAdmin || $_SESSION['tipo_usuario'] === 'instructor'): ?>
        <h2>Crear / Editar Zonas</h2>

        <table border="1" style="width:100%; border-collapse: collapse;">
            <thead>
            <tr>
                <th style="padding: 8px; text-align: left; width: 25%;">Nombre</th>
                <th style="padding: 8px; text-align: left; width: 35%;">Descripción</th>
                <th style="padding: 8px; text-align: left; width: 25%;">Imagen</th>
                <th style="padding: 8px; text-align: left; width: 15%;">Acción</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <form method="post" action="../action/cuerpoZonaAction.php" enctype="multipart/form-data"
                      onsubmit="return confirm('¿Estás seguro de que deseas crear este nuevo registro?');">
                    <td style="padding: 8px;">
                        <input type="text" name="tbcuerpozonanombre" placeholder="Ej: Pecho" required
                               style="width: 100%; box-sizing: border-box;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" name="tbcuerpozonadescripcion" placeholder="Ej: Músculos pectorales" required
                               style="width: 100%; box-sizing: border-box;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="file" name="imagen" accept="image/png, image/jpeg, image/webp">
                    </td>
                    <td style="padding: 8px; text-align: center;">
                        <input type="submit" value="Crear" name="create" style="width: 90%;">
                    </td>
                </form>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <?php
    $cuerpoZonaBusiness = new CuerpoZonaBusiness();

    if ($esAdmin || $_SESSION['tipo_usuario'] === 'instructor') {
        // Los administradores e instructores pueden ver todas las zonas (activas e inactivas)
        $allCuerpoZonas = $cuerpoZonaBusiness->getAllTBCuerpoZona();

        $zonasActivas = [];
        $zonasInactivas = [];

        foreach ($allCuerpoZonas as $zona) {
            if ($zona->getActivoCuerpoZona() == 1) {
                $zonasActivas[] = $zona;
            } else {
                $zonasInactivas[] = $zona;
            }
        }
    } else {
        // Los clientes solo pueden ver zonas activas
        $zonasActivas = $cuerpoZonaBusiness->getActiveTBCuerpoZona();
        $zonasInactivas = []; // Lista vacía para clientes
    }
    ?>

    <h2>Zonas Activas</h2>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="padding: 8px; text-align: left; width: 25%;">Nombre</th>
            <th style="padding: 8px; text-align: left; width: 35%;">Descripción</th>
            <th style="padding: 8px; text-align: left; width: 25%;">Imagen</th>
            <th style="padding: 8px; text-align: left; width: 15%;">Acción</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($zonasActivas)): ?>
            <tr>
                <td colspan="4" style="text-align: center;">No hay zonas activas.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($zonasActivas as $current): ?>
                <tr>
                    <?php if ($esAdmin || $_SESSION['tipo_usuario'] === 'instructor'): ?>
                        <form method="post" action="../action/cuerpoZonaAction.php" enctype="multipart/form-data">
                            <input type="hidden" name="tbcuerpozonaid" value="<?= $current->getIdCuerpoZona() ?>">
                            <input type="hidden" name="tbcuerpozonaactivo" value="1">
                            <td style="padding: 8px;"><input type="text" name="tbcuerpozonanombre"
                                                             value="<?= $current->getNombreCuerpoZona() ?>"
                                                             style="width: 100%; box-sizing: border-box;"></td>
                            <td style="padding: 8px;"><input type="text" name="tbcuerpozonadescripcion"
                                                             value="<?= $current->getDescripcionCuerpoZona() ?>"
                                                             style="width: 100%; box-sizing: border-box;"></td>

                            <!-- Celda para la gestión de imágenes -->
                            <td style="padding: 8px;" data-image-manager>
                                <?php
                                $nombreImagen = 'cuerpo_zonas_' . $current->getIdCuerpoZona() . '.jpg';
                                $rutaImagen = '../img/cuerpo_zonas/' . $nombreImagen;
                                ?>
                                <div class="imagen-actual-container" <?= !file_exists($rutaImagen) ? ' style="display: none;"' : '' ?>>
                                    <img src="<?= $rutaImagen ?>?t=<?= time() ?>" alt="Imagen actual"
                                         style="max-width: 100px; max-height: 100px; display: block; margin-bottom: 5px;">
                                    <button type="button" class="eliminar-imagen-btn" style="cursor: pointer;">X
                                    </button>
                                </div>
                                <div class="input-imagen-container" <?= file_exists($rutaImagen) ? ' style="display: none;"' : '' ?>>
                                    <input type="file" name="imagen" accept="image/png, image/jpeg, image/webp">
                                </div>
                                <input type="hidden" name="eliminar_imagen" value="0">
                            </td>

                            <td style="padding: 8px; text-align: center;">
                                <input type="submit" value="Actualizar" name="update"
                                       style="margin-bottom: 5px; width: 90%;"
                                       onclick="return confirm('¿Estás seguro de que deseas actualizar este registro?');">
                                <input type="submit" value="Desactivar" name="desactivar" style="width: 90%;"
                                       onclick="return confirm('¿Estás seguro de que deseas desactivar este registro?');">
                            </td>
                        </form>
                    <?php else: ?>
                        <!-- Vista de solo lectura para clientes -->
                        <td style="padding: 8px;"><?= $current->getNombreCuerpoZona() ?></td>
                        <td style="padding: 8px;"><?= $current->getDescripcionCuerpoZona() ?></td>
                        <td style="padding: 8px;">
                            <?php
                            $nombreImagen = 'cuerpo_zonas_' . $current->getIdCuerpoZona() . '.jpg';
                            $rutaImagen = '../img/cuerpo_zonas/' . $nombreImagen;
                            if (file_exists($rutaImagen)) {
                                echo '<img src="' . $rutaImagen . '?t=' . time() . '" alt="Imagen" style="max-width: 100px; max-height: 100px;">';
                            } else {
                                echo 'Sin imagen';
                            }
                            ?>
                        </td>
                        <td style="padding: 8px; text-align: center;">Solo vista</td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <br>

    <?php if ($esAdmin || $_SESSION['tipo_usuario'] === 'instructor'): ?>
        <h2>Zonas Inactivas</h2>
        <table border="1" style="width:100%; border-collapse: collapse;">
            <thead>
            <tr>
                <th style="padding: 8px; text-align: left; width: 25%;">Nombre</th>
                <th style="padding: 8px; text-align: left; width: 35%;">Descripción</th>
                <th style="padding: 8px; text-align: left; width: 25%;">Imagen</th>
                <th style="padding: 8px; text-align: left; width: 15%;">Acción</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($zonasInactivas)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No hay zonas inactivas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($zonasInactivas as $current): ?>
                    <tr>
                        <form method="post" action="../action/cuerpoZonaAction.php">
                            <input type="hidden" name="tbcuerpozonaid" value="<?= $current->getIdCuerpoZona() ?>">
                            <td style="padding: 8px;"><?= $current->getNombreCuerpoZona() ?></td>
                            <td style="padding: 8px;"><?= $current->getDescripcionCuerpoZona() ?></td>
                            <td style="padding: 8px;">
                                <?php
                                $nombreImagen = 'cuerpo_zonas_' . $current->getIdCuerpoZona() . '.jpg';
                                $rutaImagen = '../img/cuerpo_zonas/' . $nombreImagen;
                                if (file_exists($rutaImagen)) {
                                    echo '<img src="' . $rutaImagen . '?t=' . time() . '" alt="Imagen" style="max-width: 100px; max-height: 100px;">';
                                } else {
                                    echo 'Sin imagen';
                                }
                                ?>
                            </td>
                            <td style="padding: 8px; text-align: center;">
                                <input type="submit" value="Activar" name="activar" style="width: 90%;"
                                       onclick="return confirm('¿Estás seguro de que deseas activar este registro?');">
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>

    <div>
        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == "emptyField") {
                echo '<p><b>Error: Hay campos vacíos.</b></p>';
            } else if ($_GET['error'] == "dbError") {
                echo '<p><b>Error: No se pudo procesar la transacción en la base de datos.</b></p>';
            } else if ($_GET['error'] == "error") {
                echo '<p><b>Error: Ocurrió un error inesperado.</b></p>';
            } else if ($_GET['error'] == "duplicateZone") {
                echo '<p><b>Error: La zona del cuerpo ya existe. No se pueden crear zonas duplicadas.</b></p>';
            } else if ($_GET['error'] == "unauthorized") {
                echo '<p><b>Error: No tiene permisos para realizar esta acción. Solo los administradores pueden crear, editar o cambiar el estado de las zonas del cuerpo.</b></p>';
            }
        }
        if (isset($_GET['success'])) {
            if ($_GET['success'] == "inserted") {
                echo '<p><b>Éxito: Registro insertado correctamente.</b></p>';
            } else if ($_GET['success'] == "updated") {
                echo '<p><b>Éxito: Registro actualizado correctamente.</b></p>';
            } else if ($_GET['success'] == "deactivated") {
                echo '<p><b>Éxito: Registro desactivado correctamente.</b></p>';
            } else if ($_GET['success'] == "activated") {
                echo '<p><b>Éxito: Registro activado correctamente.</b></p>';
            }
        }
        ?>
    </div>
</main>

<hr>

<footer>
    <p>Fin de la página.</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Delegación de eventos para los botones 'X' y los inputs de archivo
        document.addEventListener('click', function (event) {
            // Si se hizo clic en un botón de eliminar imagen
            if (event.target.classList.contains('eliminar-imagen-btn')) {
                const manager = event.target.closest('[data-image-manager]');
                if (manager) {
                    const imagenActualContainer = manager.querySelector('.imagen-actual-container');
                    const inputContainer = manager.querySelector('.input-imagen-container');
                    const hiddenEliminar = manager.querySelector('input[name="eliminar_imagen"]');

                    imagenActualContainer.style.display = 'none';
                    inputContainer.style.display = 'block';
                    hiddenEliminar.value = '1';
                }
            }
        });

        document.addEventListener('change', function (event) {
            // Si se seleccionó un archivo en un input de imagen
            if (event.target.matches('input[type="file"][name="imagen"]')) {
                const inputImagen = event.target;
                const manager = inputImagen.closest('[data-image-manager]');
                const inputContainer = inputImagen.parentElement;

                const [file] = inputImagen.files;
                if (file) {
                    // Limpiar previsualización anterior si existe
                    const oldPreview = inputContainer.querySelector('img.preview');
                    if (oldPreview) {
                        oldPreview.remove();
                    }

                    // Crear y mostrar nueva previsualización
                    const preview = document.createElement('img');
                    preview.src = URL.createObjectURL(file);
                    preview.alt = 'Previsualización de nueva imagen';
                    preview.className = 'preview';
                    preview.style.maxWidth = '100px';
                    preview.style.maxHeight = '100px';
                    preview.style.marginTop = '10px';
                    inputContainer.appendChild(preview);

                    // Si hay un campo oculto de eliminar, anular la orden
                    if (manager) {
                        const hiddenEliminar = manager.querySelector('input[name="eliminar_imagen"]');
                        if (hiddenEliminar) {
                            hiddenEliminar.value = '0';
                        }
                    }
                }
            }
        });

    });
</script>

</body>
</html>