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
                    <th style="padding: 8px; text-align: left;">Activo</th>
                    <th style="padding: 8px; text-align: left;">Acción</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <form method="post" action="../action/zonaCuerpoAction.php" onsubmit="return confirm('¿Estás seguro de que deseas crear este nuevo registro?');">
                        <td style="padding: 8px;">
                            <input type="text" name="tbzonacuerponombre" placeholder="Ej: Pecho" required style="width: 95%;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="text" name="tbzonacuerpodescripcion" placeholder="Ej: Músculos pectorales" required style="width: 95%;">
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
                    echo '<form method="post" action="../action/zonaCuerpoAction.php">';

                    echo '<input type="hidden" name="tbzonacuerpoid" value="' . $current->getIdZonaCuerpo() . '">';

                    echo '<td style="padding: 8px;"><input type="text" name="tbzonacuerponombre" value="' . $current->getNombreZonaCuerpo() . '" style="width: 95%;"></td>';
                    echo '<td style="padding: 8px;"><input type="text" name="tbzonacuerpodescripcion" value="' . $current->getDescripcionZonaCuerpo() . '" style="width: 95%;"></td>';
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

</body>
</html>