<?php
session_start();
include_once '../business/PadecimientoDictamenBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../business/clientePadecimientoBusiness.php';
include_once '../utility/Validation.php';
Validation::start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');
$esCliente = ($_SESSION['tipo_usuario'] === 'cliente');

if (!$esAdminOInstructor && !$esCliente) {
    header("location: ../view/loginView.php");
    exit();
}

$padecimientoDictamenBusiness = new PadecimientoDictamenBusiness();
$imageManager = new ImageManager();
$clientePadecimientoBusiness = new ClientePadecimientoBusiness();
$padecimientosDictamen = $padecimientoDictamenBusiness->getAllTBPadecimientoDictamen();
$clientes = $esAdminOInstructor ? $clientePadecimientoBusiness->obtenerTodosLosClientes() : [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Dictamen Médico</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Gestión de Dictamen Médico</h2>
        </header>

        <main>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message flash-msg">¡Operación realizada con éxito!</p>
            <?php endif; ?>
            <?php if (Validation::hasErrors()): ?>
                <div class="error-message flash-msg">
                    <strong>Por favor corrija los errores:</strong>
                    <ul>
                        <?php foreach (Validation::getAllErrors() as $error): ?>
                            <li>
                                <?= htmlspecialchars($error); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Dictamen</h3>
                <form id="crearPadecimientoForm" action="../action/PadecimientoDictamenAction.php" method="POST"
                    enctype="multipart/form-data">
                    <div class="form-grid-container">
                        <?php if ($esAdminOInstructor): ?>
                            <div class="form-group">
                                <label for="clienteId">Cliente:</label>
                                <select name="clienteId" id="clienteId">
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= htmlspecialchars($cliente['id']) ?>"
                                            <?= Validation::getOldInput('clienteId') == $cliente['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cliente['carnet'] . ' - ' . $cliente['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="fechaemision">Fecha de Emisión:</label>
                            <input type="date" name="fechaemision" id="fechaemision" max="<?= date('Y-m-d') ?>"
                                value="<?= htmlspecialchars(Validation::getOldInput('fechaemision')); ?>">
                        </div>

                        <div class="form-group">
                            <label for="entidademision">Entidad de Emisión:</label>
                            <input type="text" name="entidademision" id="entidademision"
                                placeholder="Nombre de la entidad"
                                value="<?= htmlspecialchars(Validation::getOldInput('entidademision')); ?>">
                        </div>
                    </div>
                    <div class="form-group form-group-horizontal" style="margin-top: 1rem;">
                        <label for="imagenes">Imágenes del Dictamen:</label>
                        <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*">
                    </div>
                    <button type="submit" name="accion" value="guardar"><i class="ph ph-plus"></i> Guardar</button>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-list-bullets"></i> Dictámenes Registrados</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                            <tr>
                                <th>Fecha de Emisión</th>
                                <th>Entidad de Emisión</th>
                                <th>Imágenes</th>
                                <?php if ($esAdminOInstructor): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($padecimientosDictamen)): ?>
                                <tr>
                                    <td colspan="4">No hay dictámenes registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($padecimientosDictamen as $padecimiento): ?>
                                    <tr data-id="<?= $padecimiento->getPadecimientodictamenid() ?>">
                                        <td data-label="Fecha de Emisión">
                                            <input type="date" name="fechaemision"
                                                value="<?= htmlspecialchars($padecimiento->getPadecimientodictamenfechaemision()) ?>"
                                                max="<?= date('Y-m-d') ?>">
                                        </td>
                                        <td data-label="Entidad de Emisión">
                                            <input type="text" name="entidademision"
                                                value="<?= htmlspecialchars($padecimiento->getPadecimientodictamenentidademision()) ?>">
                                        </td>
                                        <td data-label="Imágenes">
                                            <div
                                                style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                                                <?php
                                                $imagenes = $imageManager->getImagesByIds($padecimiento->getPadecimientodictamenimagenid());
                                                if (empty($imagenes)) {
                                                    echo 'Sin imágenes';
                                                }
                                                foreach ($imagenes as $img) {
                                                    echo '<div class="image-container" style="width: 60px; height: 60px;"><img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen" onerror="this.onerror=null; this.src=\'noimage.svg\';">';
                                                    if ($esAdminOInstructor) {
                                                        echo '<button type="button" class="delete-image-btn" data-image-id="' . $img['tbimagenid'] . '"><i class="ph ph-x"></i></button>';
                                                    }
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                            <input type="file" name="imagenes[]" multiple accept="image/*"
                                                style="margin-top: 0.5rem;">
                                        </td>
                                        <?php if ($esAdminOInstructor): ?>
                                            <td data-label="Acciones">
                                                <div class="actions">
                                                    <button type="button" class="btn-row seleccionar-btn" title="Seleccionar"><i class="ph ph-check"></i></button>
                                                    <button type="button" class="btn-row actualizar-btn"
                                                        title="Actualizar"><i class="ph ph-pencil-simple"></i></button>
                                                    <button type="button" class="btn-row btn-danger eliminar-btn"
                                                        title="Eliminar"><i class="ph ph-trash"></i></button>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <?php Validation::clear(); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.seleccionar-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.dataset.id;
                    const entidad = row.querySelector('input[name="entidademision"]').value;
                    
                    if (window.opener && !window.opener.closed) {
                        window.opener.seleccionarDictamen(id, entidad);
                        window.close();
                    }
                });
            });
        });
    </script>
</body>

</html>