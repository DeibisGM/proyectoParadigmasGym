<?php
session_start();
include_once '../business/PadecimientoDictamenBusiness.php';
include_once '../utility/ImageManager.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');
$esCliente = ($_SESSION['tipo_usuario'] === 'cliente');

// Permitir acceso a admin, instructor y cliente
if (!$esAdminOInstructor && !$esCliente) {
    header("location: ../view/loginView.php");
    exit();
}

$padecimientoDictamenBusiness = new PadecimientoDictamenBusiness();
$imageManager = new ImageManager();

// Por ahora, obtener todos los padecimientos dictamen (luego filtraremos por funcionalidad)
$padecimientosDictamen = $padecimientoDictamenBusiness->getAllTBPadecimientoDictamen();

// Obtener lista de clientes para admin/instructor
$clientes = [];
if ($esAdminOInstructor) {
    $clientes = $padecimientoDictamenBusiness->getAllClientes();
}

// Mensajes de estado
$mensaje = '';
$tipoMensaje = '';

if (isset($_GET['success'])) {
    $tipoMensaje = 'success';
    switch ($_GET['success']) {
        case 'inserted':
            $mensaje = 'Padecimiento dictamen creado exitosamente.';
            break;
        case 'updated':
            $mensaje = 'Padecimiento dictamen actualizado exitosamente.';
            break;
        case 'eliminado':
            $mensaje = 'Padecimiento dictamen eliminado exitosamente.';
            break;
        case 'image_deleted':
            $mensaje = 'Imagen eliminada exitosamente.';
            break;
    }
}

if (isset($_GET['error'])) {
    $tipoMensaje = 'error';
    switch ($_GET['error']) {
        case 'datos_faltantes':
            $mensaje = 'Error: Faltan datos obligatorios.';
            break;
        case 'fecha_futura':
            $mensaje = 'Error: La fecha de emisión no puede ser futura.';
            break;
        case 'cliente_no_encontrado':
            $mensaje = 'Error: Cliente no encontrado con ese carnet.';
            break;
        case 'cliente_requerido':
            $mensaje = 'Error: Debe seleccionar un cliente.';
            break;
        case 'insertar':
            $mensaje = 'Error al crear el padecimiento dictamen.';
            break;
        case 'dbError':
            $mensaje = 'Error en la base de datos.';
            break;
        case 'notFound':
            $mensaje = 'Padecimiento dictamen no encontrado.';
            break;
        case 'eliminar':
            $mensaje = 'Error al eliminar el padecimiento dictamen.';
            break;
        case 'unauthorized':
            $mensaje = 'No tiene permisos para realizar esta acción.';
            break;
        case 'id_faltante':
            $mensaje = 'Error: ID faltante.';
            break;
        case 'accion_no_valida':
            $mensaje = 'Error: Acción no válida.';
            break;
        case 'exception':
            $mensaje = 'Error del sistema: ' . (isset($_GET['msg']) ? $_GET['msg'] : 'Error desconocido');
            break;
        default:
            $mensaje = 'Ha ocurrido un error.';
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Padecimientos Dictamen</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i> Volver al Inicio</a><br><br>
        <h2><i class="ph ph-file-text"></i> Padecimientos Dictamen</h2>
        <?php if ($esCliente): ?>
            <p><small>Gestione sus padecimientos dictamen</small></p>
        <?php endif; ?>
    </header>
    <hr>

    <?php if (!empty($mensaje)): ?>
        <div id="mensaje" class="<?= $tipoMensaje ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <main>
        <!-- Crear nuevo padecimiento dictamen -->
        <section>
            <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Padecimiento Dictamen</h3>
            <form method="post" action="../action/PadecimientoDictamenAction.php" enctype="multipart/form-data">
                <div class="form-grid">
                    <?php if ($esAdminOInstructor): ?>
                        <!-- Solo admin e instructor pueden seleccionar cliente -->
                        <div class="form-group">
                            <label for="cliente_carnet">Cliente (Carnet):</label>
                            <input type="text" name="cliente_carnet" id="cliente_carnet"
                                   placeholder="Ingrese el carnet del cliente" required
                                   list="clientes-list">
                            <datalist id="clientes-list">
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= htmlspecialchars($cliente['tbclientecarnet']) ?>">
                                        <?= htmlspecialchars($cliente['tbclientecarnet'] . ' - ' . $cliente['tbclientenombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </datalist>
                            <small>Seleccione o escriba el carnet del cliente</small>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="fechaemision">Fecha de Emisión:</label>
                        <input type="date" name="fechaemision" id="fechaemision" required max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label for="entidademision">Entidad de Emisión:</label>
                        <input type="text" name="entidademision" id="entidademision" placeholder="Nombre de la entidad" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="imagenes">Imágenes del Dictamen:</label>
                        <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*">
                        <small>Formatos aceptados: JPG, PNG, WebP. Máximo 5MB por imagen.</small>
                    </div>
                </div>
                <button type="submit" name="guardar"><i class="ph ph-plus"></i> Guardar Padecimiento Dictamen</button>
            </form>
        </section>

        <!-- Listado de padecimientos dictamen -->
        <section>
            <h3><i class="ph ph-list-bullets"></i> Padecimientos Dictamen Registrados</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Fecha de Emisión</th>
                        <th>Entidad de Emisión</th>
                        <th>Imágenes</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($padecimientosDictamen)): ?>
                        <?php foreach ($padecimientosDictamen as $padecimiento): ?>
                            <tr>
                                <form method="post" action="../action/PadecimientoDictamenAction.php" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= $padecimiento->getPadecimientodictamenid() ?>">

                                    <td>
                                        <input type="date" name="fechaemision"
                                               value="<?= htmlspecialchars($padecimiento->getPadecimientodictamenfechaemision()) ?>"
                                               required max="<?= date('Y-m-d') ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="entidademision"
                                               value="<?= htmlspecialchars($padecimiento->getPadecimientodictamenentidademision()) ?>"
                                               required>
                                    </td>
                                    <td>
                                        <div class="image-gallery">
                                            <?php
                                            $imagenes = $imageManager->getImagesByIds($padecimiento->getPadecimientodictamenimagenid());
                                            if (empty($imagenes)) {
                                                echo '<span style="color: #6c757d;">Sin imágenes</span>';
                                            } else {
                                                foreach ($imagenes as $img) {
                                                    echo '<div class="image-container">';
                                                    echo '<img src="..' . htmlspecialchars($img['tbimagenruta'] ?? '') . '?t=' . time() . '" alt="Imagen del dictamen">';
                                                    // Solo admin e instructor pueden borrar imágenes
                                                    if ($esAdminOInstructor) {
                                                        echo '<button type="submit" name="borrar_imagen" value="' . $img['tbimagenid'] . '" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button>';
                                                    }
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <label>Añadir más imágenes:</label>
                                        <input type="file" name="imagenes[]" multiple accept="image/*">
                                    </td>

                                    <td class="actions-cell">
                                        <button type="submit" name="actualizar" title="Actualizar">
                                            <i class="ph ph-floppy-disk"></i> Actualizar
                                        </button>

                                        <?php if ($esAdminOInstructor): ?>
                                            <button type="submit" name="eliminar"
                                                    onclick="return confirm('¿Está seguro de eliminar este padecimiento dictamen?');"
                                                    title="Eliminar">
                                                <i class="ph ph-trash"></i> Eliminar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #6c757d; font-style: italic;">
                                No hay padecimientos dictamen registrados
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date("Y") ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>

<script>
// Auto-ocultar mensajes después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const mensaje = document.getElementById('mensaje');
    if (mensaje) {
        setTimeout(function() {
            mensaje.style.opacity = '0';
            setTimeout(function() {
                mensaje.style.display = 'none';
            }, 300);
        }, 5000);
    }
});

// Validación de fecha en el cliente
document.addEventListener('DOMContentLoaded', function() {
    const fechaInputs = document.querySelectorAll('input[type="date"]');
    const hoy = new Date().toISOString().split('T')[0];

    fechaInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            if (this.value > hoy) {
                alert('La fecha de emisión no puede ser futura.');
                this.value = hoy;
            }
        });
    });
});

// Validación de cliente carnet (solo para admin/instructor)
<?php if ($esAdminOInstructor): ?>
document.addEventListener('DOMContentLoaded', function() {
    const clienteCarnetInput = document.getElementById('cliente_carnet');
    if (clienteCarnetInput) {
        clienteCarnetInput.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.setCustomValidity('Debe seleccionar un cliente');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
<?php endif; ?>
</script>

</body>
</html>