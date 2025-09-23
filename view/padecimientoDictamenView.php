<?php
session_start();
include_once '../business/PadecimientoDictamenBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../business/clientePadecimientoBusiness.php';
include_once '../utility/Validation.php'; // AGREGADO
Validation::start(); // AGREGADO

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

$clientes = [];
if ($esAdminOInstructor) {
    $clientes = $clientePadecimientoBusiness->obtenerTodosLosClientes();
}

// AGREGADO: Manejo de mensajes de éxito
$successMessage = null;
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created': $successMessage = 'Padecimiento dictamen creado con éxito.'; break;
        case 'updated': $successMessage = 'Padecimiento dictamen actualizado con éxito.'; break;
        case 'deleted': $successMessage = 'Padecimiento dictamen eliminado con éxito.'; break;
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
    <style>
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            display: block;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 0.25rem;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        .error-general {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 0.25rem;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        .error-general ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.5rem;
        }
        .error-general li {
            margin-bottom: 0.25rem;
        }
        .form-field {
            margin-bottom: 1rem;
        }
        .input-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
    </style>
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

    <div id="mensaje" style="display:none;"></div>

    <!-- AGREGADO: Mostrar mensaje de éxito -->
    <?php if ($successMessage): ?>
        <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <!-- AGREGADO: Mostrar mensaje de error general -->
    <?php if (Validation::hasErrors()): ?>
        <div class="error-general">
            <strong>Por favor corrija los siguientes errores:</strong>
            <ul>
                <?php if ($esAdminOInstructor && Validation::getError('clienteId')): ?>
                    <li>Debe seleccionar un cliente</li>
                <?php endif; ?>
                <?php if (Validation::getError('fechaemision')): ?>
                    <li>Debe seleccionar una fecha de emisión válida</li>
                <?php endif; ?>
                <?php if (Validation::getError('entidademision')): ?>
                    <li>Debe ingresar la entidad de emisión</li>
                <?php endif; ?>
                <?php if (Validation::getError('imagenes')): ?>
                    <li>Hay un problema con las imágenes seleccionadas</li>
                <?php endif; ?>
                <?php if ($generalError = Validation::getError('general')): ?>
                    <li><?= htmlspecialchars($generalError); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <main>
        <section>
            <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Padecimiento Dictamen</h3>
            <form id="crearPadecimientoForm" action="../action/PadecimientoDictamenAction.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <?php if ($esAdminOInstructor): ?>
                        <div class="form-field">
                            <?php if ($error = Validation::getError('clienteId')): ?>
                                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <label for="clienteId">Cliente:</label>
                            <select name="clienteId" id="clienteId" <?= Validation::getError('clienteId') ? 'class="input-error"' : '' ?>>
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

                    <div class="form-field">
                        <?php if ($error = Validation::getError('fechaemision')): ?>
                            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <label for="fechaemision">Fecha de Emisión:</label>
                        <input type="date" name="fechaemision" id="fechaemision" max="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars(Validation::getOldInput('fechaemision')); ?>"
                               <?= Validation::getError('fechaemision') ? 'class="input-error"' : '' ?>>
                    </div>

                    <div class="form-field">
                        <?php if ($error = Validation::getError('entidademision')): ?>
                            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <label for="entidademision">Entidad de Emisión:</label>
                        <input type="text" name="entidademision" id="entidademision" placeholder="Nombre de la entidad"
                               value="<?= htmlspecialchars(Validation::getOldInput('entidademision')); ?>"
                               <?= Validation::getError('entidademision') ? 'class="input-error"' : '' ?>>
                    </div>

                    <div class="form-field full-width">
                        <?php if ($error = Validation::getError('imagenes')): ?>
                            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <label for="imagenes">Imágenes del Dictamen:</label>
                        <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*">
                        <small>Formatos aceptados: JPG, PNG, WebP. Máximo 5MB por imagen.</small>
                    </div>
                </div>
                <button type="submit"><i class="ph ph-plus"></i> Guardar Padecimiento Dictamen</button>
            </form>
        </section>

        <hr>

        <section>
            <h3><i class="ph ph-list-bullets"></i> Padecimientos Dictamen Registrados</h3>
            <div style="overflow-x:auto;">
                <table id="padecimientoTabla">
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
                            <tr data-id="<?= $padecimiento->getPadecimientodictamenid() ?>">
                                <td>
                                    <input type="date" name="fechaemision"
                                           value="<?= htmlspecialchars($padecimiento->getPadecimientodictamenfechaemision()) ?>"
                                           max="<?= date('Y-m-d') ?>">
                                </td>
                                <td>
                                    <input type="text" name="entidademision"
                                           value="<?= htmlspecialchars($padecimiento->getPadecimientodictamenentidademision()) ?>">
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
                                                if ($esAdminOInstructor) {
                                                    echo '<button type="button" class="delete-image-btn" data-image-id="' . $img['tbimagenid'] . '">X</button>';
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
                                    <button type="button" class="actualizar-btn" title="Actualizar">
                                        <i class="ph ph-floppy-disk"></i> Actualizar
                                    </button>
                                    <?php if ($esAdminOInstructor): ?>
                                        <button type="button" class="eliminar-btn" title="Eliminar">
                                            <i class="ph ph-trash"></i> Eliminar
                                        </button>
                                    <?php endif; ?>
                                </td>
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
    const ACTION_URL = '../action/PadecimientoDictamenAction.php';

    function mostrarMensaje(mensaje, tipo) {
        const mensajeDiv = document.getElementById('mensaje');
        mensajeDiv.textContent = mensaje;
        mensajeDiv.className = tipo;
        mensajeDiv.style.display = 'block';
        setTimeout(() => {
            mensajeDiv.style.opacity = '0';
            setTimeout(() => {
                mensajeDiv.style.display = 'none';
            }, 300);
        }, 5000);
    }

    async function manejarRespuesta(response) {
        if (!response.ok) {
            throw new Error('Error en la red o en el servidor.');
        }
        const data = await response.json();
        if (data.success) {
            mostrarMensaje(data.message, 'success');
        } else {
            mostrarMensaje(data.message, 'error');
        }
        return data;
    }

    function crearFilaTabla(padecimiento) {
        const esAdminOInstructor = <?= $esAdminOInstructor ? 'true' : 'false' ?>;

        const row = document.createElement('tr');
        row.dataset.id = padecimiento.id;
        row.innerHTML = `
            <td>
                <input type="date" name="fechaemision" value="${padecimiento.fechaemision}" max="<?= date('Y-m-d') ?>">
            </td>
            <td>
                <input type="text" name="entidademision" value="${padecimiento.entidademision}">
            </td>
            <td>
                <div class="image-gallery">
                    ${padecimiento.imagenes.map(img => `
                        <div class="image-container">
                            <img src="${img.ruta}?t=${Date.now()}" alt="Imagen del dictamen">
                            ${esAdminOInstructor ? `<button type="button" class="delete-image-btn" data-image-id="${img.id}">X</button>` : ''}
                        </div>
                    `).join('')}
                    ${padecimiento.imagenes.length === 0 ? '<span style="color: #6c757d;">Sin imágenes</span>' : ''}
                </div>
                <label>Añadir más imágenes:</label>
                <input type="file" name="imagenes[]" multiple accept="image/*">
            </td>
            <td class="actions-cell">
                <button type="button" class="actualizar-btn" title="Actualizar">
                    <i class="ph ph-floppy-disk"></i> Actualizar
                </button>
                ${esAdminOInstructor ? `
                    <button type="button" class="eliminar-btn" title="Eliminar">
                        <i class="ph ph-trash"></i> Eliminar
                    </button>
                ` : ''}
            </td>
        `;
        return row;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('crearPadecimientoForm').addEventListener('submit', (e) => {
            // No prevenir el comportamiento por defecto para permitir envío normal del formulario
            const form = e.target;

            // Crear un input hidden para la acción
            let accionInput = form.querySelector('input[name="accion"]');
            if (!accionInput) {
                accionInput = document.createElement('input');
                accionInput.type = 'hidden';
                accionInput.name = 'accion';
                accionInput.value = 'guardar';
                form.appendChild(accionInput);
            }

            // El formulario se enviará normalmente a la acción y redirigirá de vuelta con los errores o éxito
        });

        document.getElementById('padecimientoTabla').addEventListener('click', async (e) => {
            if (e.target.closest('.actualizar-btn')) {
                const row = e.target.closest('tr');
                const id = row.dataset.id;
                const fechaemision = row.querySelector('input[name="fechaemision"]').value;
                const entidademision = row.querySelector('input[name="entidademision"]').value;
                const imagenes = row.querySelector('input[name="imagenes[]"]').files;

                const formData = new FormData();
                formData.append('accion', 'actualizar');
                formData.append('id', id);
                formData.append('fechaemision', fechaemision);
                formData.append('entidademision', entidademision);

                if (imagenes.length > 0) {
                    for (const file of imagenes) {
                        formData.append('imagenes[]', file);
                    }
                }

                try {
                    const response = await fetch(ACTION_URL, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await manejarRespuesta(response);

                    if (data.success && data.padecimiento) {
                         const imageGallery = row.querySelector('.image-gallery');
                         const esAdminOInstructor = <?= $esAdminOInstructor ? 'true' : 'false' ?>;
                         imageGallery.innerHTML = data.padecimiento.imagenes.map(img => `
                            <div class="image-container">
                                <img src="${img.ruta}?t=${Date.now()}" alt="Imagen del dictamen">
                                ${esAdminOInstructor ? `<button type="button" class="delete-image-btn" data-image-id="${img.id}">X</button>` : ''}
                            </div>
                         `).join('') + (data.padecimiento.imagenes.length === 0 ? '<span style="color: #6c757d;">Sin imágenes</span>' : '');

                         // Limpiar el input de archivos después de actualizar
                         row.querySelector('input[name="imagenes[]"]').value = '';
                    }

                } catch (error) {
                    mostrarMensaje('Error al conectar con el servidor: ' + error.message, 'error');
                }
            }

            if (e.target.closest('.eliminar-btn')) {
                const row = e.target.closest('tr');
                const id = row.dataset.id;

                if (confirm('¿Está seguro de eliminar este padecimiento dictamen?')) {
                    const formData = new FormData();
                    formData.append('accion', 'eliminar');
                    formData.append('id', id);

                    try {
                        const response = await fetch(ACTION_URL, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await manejarRespuesta(response);

                        if (data.success) {
                            row.remove();

                            // Verificar si la tabla está vacía y mostrar mensaje
                            const tablaBody = document.querySelector('#padecimientoTabla tbody');
                            if (!tablaBody.querySelector('tr[data-id]')) {
                                tablaBody.innerHTML = `
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #6c757d; font-style: italic;">
                                            No hay padecimientos dictamen registrados
                                        </td>
                                    </tr>
                                `;
                            }
                        }
                    } catch (error) {
                        mostrarMensaje('Error al conectar con el servidor: ' + error.message, 'error');
                    }
                }
            }

            if (e.target.closest('.delete-image-btn')) {
                const button = e.target.closest('.delete-image-btn');
                const imageId = button.dataset.imageId;
                const row = button.closest('tr');
                const padecimientoId = row.dataset.id;

                if (confirm('¿Eliminar esta imagen?')) {
                    const formData = new FormData();
                    formData.append('accion', 'borrar_imagen');
                    formData.append('padecimiento_id', padecimientoId);
                    formData.append('imagen_id', imageId);

                    try {
                        const response = await fetch(ACTION_URL, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await manejarRespuesta(response);
                        if (data.success) {
                            button.closest('.image-container').remove();
                            const imageGallery = row.querySelector('.image-gallery');
                            if (imageGallery.children.length === 0) {
                                imageGallery.innerHTML = '<span style="color: #6c757d;">Sin imágenes</span>';
                            }
                        }
                    } catch (error) {
                        mostrarMensaje('Error al conectar con el servidor: ' + error.message, 'error');
                    }
                }
            }
        });
    });
</script>
</body>
</html>