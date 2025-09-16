<?php
session_start();
include_once '../business/PadecimientoDictamenBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../business/clientePadecimientoBusiness.php'; // Incluir para obtener la lista completa de clientes

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

// Instancia la clase para obtener la lista de clientes
$clientePadecimientoBusiness = new ClientePadecimientoBusiness();

$padecimientosDictamen = $padecimientoDictamenBusiness->getAllTBPadecimientoDictamen();

$clientes = [];
if ($esAdminOInstructor) {
    $clientes = $clientePadecimientoBusiness->obtenerTodosLosClientes();
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
        /* Agrega aquí tus estilos o inclúyelos en un archivo separado */
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

    <main>
        <section>
            <h3><i class="ph ph-plus-circle"></i> Crear Nuevo Padecimiento Dictamen</h3>
            <form id="crearPadecimientoForm">
                <div class="form-grid">
                    <?php if ($esAdminOInstructor): ?>
                        <div class="form-group">
                            <label for="clienteId">Cliente:</label>
                            <select name="clienteId" id="clienteId" required>
                                <option value="">Seleccione un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= htmlspecialchars($cliente['id']) ?>">
                                        <?= htmlspecialchars($cliente['carnet'] . ' - ' . $cliente['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                <input type="date" name="fechaemision" value="${padecimiento.fechaemision}" required max="<?= date('Y-m-d') ?>">
            </td>
            <td>
                <input type="text" name="entidademision" value="${padecimiento.entidademision}" required>
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
        document.getElementById('crearPadecimientoForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('accion', 'guardar');

            // Obtener el ID del cliente del combobox
            const clienteId = document.getElementById('clienteId');
            if (clienteId) {
                formData.append('clienteId', clienteId.value);
            }

            try {
                const response = await fetch(ACTION_URL, {
                    method: 'POST',
                    body: formData
                });
                const data = await manejarRespuesta(response);

                if (data.success) {
                    form.reset();
                    const tablaBody = document.querySelector('#padecimientoTabla tbody');
                    if (tablaBody.querySelector('tr[data-id]')) {
                         tablaBody.prepend(crearFilaTabla(data.padecimiento));
                    } else {
                        tablaBody.innerHTML = '';
                        tablaBody.appendChild(crearFilaTabla(data.padecimiento));
                    }
                }
            } catch (error) {
                mostrarMensaje('Error al conectar con el servidor: ' + error.message, 'error');
            }
        });

        document.getElementById('padecimientoTabla').addEventListener('click', async (e) => {
            if (e.target.closest('.actualizar-btn')) {
                const row = e.target.closest('tr');
                const id = row.dataset.id;
                const fechaemision = row.querySelector('input[name="fechaemision"]').value;
                const entidademision = row.querySelector('input[name="entidademision"]').value;
                const imagenes = row.querySelector('input[name="imagenes[]"]').files;

                if (!fechaemision || !entidademision) {
                    mostrarMensaje('Faltan datos obligatorios para actualizar.', 'error');
                    return;
                }

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
                        body: formData
                    });
                    const data = await manejarRespuesta(response);

                    if (data.success && data.padecimiento) {
                         const imageGallery = row.querySelector('.image-gallery');
                         imageGallery.innerHTML = data.padecimiento.imagenes.map(img => `
                            <div class="image-container">
                                <img src="${img.ruta}?t=${Date.now()}" alt="Imagen del dictamen">
                                <button type="button" class="delete-image-btn" data-image-id="${img.id}">X</button>
                            </div>
                         `).join('') + (data.padecimiento.imagenes.length === 0 ? '<span style="color: #6c757d;">Sin imágenes</span>' : '');
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
                            body: formData
                        });
                        const data = await manejarRespuesta(response);

                        if (data.success) {
                            row.remove();
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
                            body: formData
                        });
                        const data = await manejarRespuesta(response);
                        if (data.success) {
                            button.closest('.image-container').remove();
                            if (row.querySelector('.image-gallery').childElementCount === 0) {
                                row.querySelector('.image-gallery').innerHTML = '<span style="color: #6c757d;">Sin imágenes</span>';
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