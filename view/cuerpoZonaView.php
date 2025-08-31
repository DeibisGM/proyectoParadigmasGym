<?php
session_start();
include_once '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
$imageManager = new ImageManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Zonas del Cuerpo</title>
    <style>
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .image-container {
            position: relative;
        }

        .image-container img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
        }

        .delete-image-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 12px;
            line-height: 1;
            padding: 2px 5px;
        }
        .clear-btn {
            display: none;
            margin-left: 10px;
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
        <h2>Crear Nueva Zona</h2>
        <form method="post" action="../action/cuerpoZonaAction.php" enctype="multipart/form-data">
            <input type="text" name="tbcuerpozonanombre" placeholder="Nombre (Ej: Pecho)" required>
            <input type="text" name="tbcuerpozonadescripcion" placeholder="Descripción" required>
            <br><label>Imágenes (puede seleccionar varias):</label><br>
            <input type="file" name="imagenes[]" accept="image/png, image/jpeg, image/webp" multiple>
            <button type="button" class="clear-btn" onclick="clearFileInput(this)">Limpiar</button>
            <br><br>
            <input type="submit" value="Crear" name="create">
        </form>
        <hr>
    <?php endif; ?>

    <?php
    $cuerpoZonaBusiness = new CuerpoZonaBusiness();
    $allCuerpoZonas = $esAdmin || $_SESSION['tipo_usuario'] === 'instructor' ?
            $cuerpoZonaBusiness->getAllTBCuerpoZona() :
            $cuerpoZonaBusiness->getActiveTBCuerpoZona();
    ?>
    <h2>Zonas Registradas</h2>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Imágenes</th>
            <?php if ($esAdmin || $_SESSION['tipo_usuario'] === 'instructor'): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($allCuerpoZonas as $current): ?>
            <tr>
                <?php if ($esAdmin || $_SESSION['tipo_usuario'] === 'instructor'): ?>

                    <form method="post" action="../action/cuerpoZonaAction.php" enctype="multipart/form-data">
                        <input type="hidden" name="tbcuerpozonaid" value="<?= $current->getIdCuerpoZona() ?>">
                        <input type="hidden" name="tbcuerpozonaactivo" value="<?= $current->getActivoCuerpoZona() ?>">
                        <td><input type="text" name="tbcuerpozonanombre"
                                   value="<?= htmlspecialchars($current->getNombreCuerpoZona() ?? '') ?>"></td>
                        <td><input type="text" name="tbcuerpozonadescripcion"
                                   value="<?= htmlspecialchars($current->getDescripcionCuerpoZona() ?? '') ?>"></td>
                        <td>
                            <div class="image-gallery">
                                <?php
                                $imagenes = $imageManager->getImagesByIds($current->getImagenesIds());
                                foreach ($imagenes as $img) {
                                    echo '<div class="image-container">';
                                    echo '<img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen">';
                                    echo '<button type="submit" name="delete_image" value="' . $img['tbimagenid'] . '" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            <br>
                            <label>Añadir más imágenes:</label><br>
                            <input type="file" name="imagenes[]" multiple>
                            <button type="button" class="clear-btn" onclick="clearFileInput(this)">Limpiar</button>
                        </td>
                        <td>
                            <button type="submit" name="update">Actualizar</button>
                            <button type="submit" name="delete"
                                    onclick="return confirm('¿Eliminar esta zona y todas sus imágenes?');">Eliminar Zona
                            </button>
                            <?php if ($current->getActivoCuerpoZona()): ?>
                                <button type="submit" name="desactivar">Desactivar</button>
                            <?php else: ?>
                                <button type="submit" name="activar">Activar</button>
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
</main>
<hr>
<script>
    function clearFileInput(button) {
        const input = button.previousElementSibling;
        if (input && input.type === 'file') {
            input.value = '';
            button.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const clearButton = this.nextElementSibling;
                if (this.files.length > 0) {
                    if (clearButton && clearButton.type === 'button') {
                        clearButton.style.display = 'inline-block';
                    }
                } else {
                    if (clearButton && clearButton.type === 'button') {
                        clearButton.style.display = 'none';
                    }
                }
            });
        });
    });
</script>
<footer><p>Fin de la página.</p></footer>
</body>
</html>