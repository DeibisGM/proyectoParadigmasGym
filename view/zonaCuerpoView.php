<?php
include '../business/zonaCuerpoBusiness.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Zonas del Cuerpo</title>
</head>
<body>

    <header>
        <h2>Gym - Zonas del Cuerpo</h2>
        <a href="../index.php">Volver al Inicio</a>
    </header>

    <hr>

    <main>
        <h2>Crear / Editar Zonas</h2>

        <table border="1" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 8px; text-align: left;">Nombre</th>
                    <th style="padding: 8px; text-align: left;">Descripción</th>
                    <th style="padding: 8px; text-align: left;">Imagen</th>
                    <th style="padding: 8px; text-align: left;">Activo</th>
                    <th style="padding: 8px; text-align: left;">Acción</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <form method="post" action="../action/zonaCuerpoAction.php" enctype="multipart/form-data" onsubmit="return confirm('¿Estás seguro de que deseas crear este nuevo registro?');">
                        <td style="padding: 8px;">
                            <input type="text" name="tbzonacuerponombre" placeholder="Ej: Pecho" required style="width: 95%;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="text" name="tbzonacuerpodescripcion" placeholder="Ej: Músculos pectorales" required style="width: 95%;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="file" name="imagen" accept="image/png, image/jpeg, image/webp">
                        </td>
                        <td style="padding: 8px;">
                            <input type="hidden" name="tbzonacuerpoactivo" value="1">
                        </td>
                        <td style="padding: 8px;">
                            <input type="submit" value="Crear" name="create">
                        </td>
                    </form>
                </tr>

                <?php
                $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
                $allZonasCuerpo = $zonaCuerpoBusiness->getAllTBZonaCuerpo();

                foreach ($allZonasCuerpo as $current) {
                    echo '<tr>';
                    echo '<form method="post" action="../action/zonaCuerpoAction.php" enctype="multipart/form-data">';

                    echo '<input type="hidden" name="tbzonacuerpoid" value="' . $current->getIdZonaCuerpo() . '">';

                    echo '<td style="padding: 8px;"><input type="text" name="tbzonacuerponombre" value="' . $current->getNombreZonaCuerpo() . '" style="width: 95%;"></td>';
                    echo '<td style="padding: 8px;"><input type="text" name="tbzonacuerpodescripcion" value="' . $current->getDescripcionZonaCuerpo() . '" style="width: 95%;"></td>';
                    
                    // Celda para la gestión de imágenes
                    echo '<td style="padding: 8px;" data-image-manager>';
                    $nombreImagen = 'zonas_cuerpo_' . $current->getIdZonaCuerpo() . '.jpg';
                    $rutaImagen = '../img/zonas_cuerpo/' . $nombreImagen;

                    // Contenedor para la imagen actual y el botón de eliminar
                    echo '<div class="imagen-actual-container"';
                    if (!file_exists($rutaImagen)) {
                        echo ' style="display: none;"'; // Ocultar si no hay imagen
                    }
                    echo '>';
                    echo '<img src="' . $rutaImagen . '?t=' . time() . '" alt="Imagen actual" style="max-width: 100px; max-height: 100px; display: block; margin-bottom: 5px;">';
                    echo '<button type="button" class="eliminar-imagen-btn" style="cursor: pointer;">X</button>';
                    echo '</div>';

                    // Contenedor para el campo de subida de archivo
                    echo '<div class="input-imagen-container"';
                    if (file_exists($rutaImagen)) {
                        echo ' style="display: none;"'; // Ocultar si ya hay una imagen
                    }
                    echo '>';
                    echo '<input type="file" name="imagen" accept="image/png, image/jpeg, image/webp">';
                    echo '</div>';

                    // Campo oculto para comunicar la intención de eliminar
                    echo '<input type="hidden" name="eliminar_imagen" value="0">';
                    echo '</td>';

                    echo '<td style="padding: 8px;">';
                    echo '<select name="tbzonacuerpoactivo">';
                    echo '<option ' . ($current->getActivoZonaCuerpo() == 1 ? "selected" : "") . ' value="1">Sí</option>';
                    echo '<option ' . ($current->getActivoZonaCuerpo() == 0 ? "selected" : "") . ' value="0">No</option>';
                    echo '</select>';
                    echo '</td>';

                    echo '<td style="padding: 8px;">';
                    echo '<input type="submit" value="Actualizar" name="update" onclick="return confirm(\'¿Estás seguro de que deseas actualizar este registro?\');"> '; 
                    echo '<input type="submit" value="Eliminar" name="delete" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.\');">';
                    echo '</td>';

                    echo '</form>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

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
                }
            } else if (isset($_GET['success'])) {
                if ($_GET['success'] == "inserted") {
                    echo '<p><b>Éxito: Registro insertado correctamente.</b></p>';
                } else if ($_GET['success'] == "updated") {
                    echo '<p><b>Éxito: Registro actualizado correctamente.</b></p>';
                } else if ($_GET['success'] == "deleted") {
                    echo '<p><b>Éxito: Registro eliminado correctamente.</b></p>';
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
document.addEventListener('DOMContentLoaded', function() {

    // Delegación de eventos para los botones 'X' y los inputs de archivo
    const tabla = document.querySelector('table tbody');

    tabla.addEventListener('click', function(event) {
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

    tabla.addEventListener('change', function(event) {
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