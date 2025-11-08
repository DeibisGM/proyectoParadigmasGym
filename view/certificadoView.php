<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

// Todos los tipos de usuario pueden ver certificados, pero con diferentes permisos
$tipoUsuario = $_SESSION['tipo_usuario'];
$puedeCrearCertificados = ($tipoUsuario === 'admin' || $tipoUsuario === 'instructor');

include_once '../business/certificadoBusiness.php';
include_once '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';

$certificadoBusiness = new CertificadoBusiness();
$instructorBusiness = new InstructorBusiness();
$imageManager = new ImageManager();

$certificados = $certificadoBusiness->getCertificados();
$instructores = $instructorBusiness->getAllTBInstructor(true);

// Obtener nombre del instructor si se filtra
$instructor_nombre = "Todos los instructores";
$instructorIdFiltro = null;

if (isset($_GET['instructor_id']) && !empty($_GET['instructor_id'])) {
    $instructorIdFiltro = $_GET['instructor_id'];
    $certificados = array_filter($certificados, function ($cert) use ($instructorIdFiltro) {
        return $cert->getIdInstructor() == $instructorIdFiltro;
    });

    // Obtener nombre del instructor
    foreach ($instructores as $instructor) {
        if ($instructor->getInstructorId() == $instructorIdFiltro) {
            $instructor_nombre = $instructor->getInstructorNombre();
            break;
        }
    }

    // Si no se encontró el instructor, usar valor por defecto
    if ($instructor_nombre === "Todos los instructores") {
        $instructor_nombre = "Instructor ID: " . htmlspecialchars($instructorIdFiltro);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Certificados</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="instructorView.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <div class="title-group">
            <h2><i class="ph ph-certificate"></i>Gestión de Certificados</h2>
            <p class="title-subtitle">Filtrado por: <strong><?= htmlspecialchars($instructor_nombre); ?></strong></p>
        </div>
    </header>

    <main>
        <?php if ($puedeCrearCertificados): ?>
            <section>
                <h3><i class="ph ph-file-plus"></i>Registrar certificado</h3>
                <form method="post" action="../action/certificadoAction.php" enctype="multipart/form-data" onsubmit="return validateCertificadoForm()">
                    <div class="form-grid-container">
                        <div class="form-group">
                            <label for="nombre"><i class="ph ph-file-text"></i>Nombre</label>
                            <input type="text" name="nombre" id="nombre" placeholder="Entrenamiento funcional" required maxlength="100">
                        </div>
                        <div class="form-group">
                            <label for="descripcion"><i class="ph ph-note-pencil"></i>Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" placeholder="Descripción breve" required>
                        </div>
                        <div class="form-group">
                            <label for="entidad"><i class="ph ph-buildings"></i>Entidad emisora</label>
                            <input type="text" name="entidad" id="entidad" placeholder="Academia o institución" required>
                        </div>
                        <div class="form-group">
                            <label for="idInstructor"><i class="ph ph-chalkboard-teacher"></i>Instructor</label>
                            <select name="idInstructor" id="idInstructor" required>
                                <option value="">Seleccione un instructor</option>
                                <?php foreach ($instructores as $instructor): ?>
                                    <option value="<?= $instructor->getInstructorId(); ?>" <?= ($instructorIdFiltro && $instructor->getInstructorId() == $instructorIdFiltro) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($instructor->getInstructorNombre() . ' (' . str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT) . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group form-group-horizontal">
                            <label for="tbcertificadoimagenid"><i class="ph ph-image"></i>Imagen del certificado</label>
                            <input type="file" name="tbcertificadoimagenid[]" id="tbcertificadoimagenid" accept="image/png, image/jpeg, image/webp">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="create"><i class="ph ph-plus-circle"></i>Registrar certificado</button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="ph ph-check-circle"></i>
                <span>
                    <?php
                    if ($_GET['success'] == 'created') echo 'Certificado creado correctamente.';
                    elseif ($_GET['success'] == 'updated') echo 'Certificado actualizado correctamente.';
                    elseif ($_GET['success'] == 'deleted') echo 'Certificado eliminado correctamente.';
                    elseif ($_GET['success'] == 'image_deleted') echo 'Imagen eliminada correctamente.';
                    ?>
                </span>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="ph ph-warning-circle"></i>
                <span>
                    <?php
                    if ($_GET['error'] == 'emptyFields') echo 'Todos los campos son obligatorios.';
                    elseif ($_GET['error'] == 'nameTooLong') echo 'El nombre es demasiado largo (máximo 100 caracteres).';
                    elseif ($_GET['error'] == 'dbError') echo 'No se pudo completar la operación en la base de datos.';
                    elseif ($_GET['error'] == 'instructorNotFound') echo 'Instructor no encontrado.';
                    elseif ($_GET['error'] == 'notFound') echo 'Certificado no encontrado.';
                    elseif ($_GET['error'] == 'image_deleted') echo 'No se pudo eliminar la imagen.';
                    else echo 'Error: ' . htmlspecialchars($_GET['error']);
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-list-bullets"></i>Certificados registrados</h3>
            <p class="section-subtitle">Visualizando resultados para <strong><?= htmlspecialchars($instructor_nombre); ?></strong>.</p>

            <?php if (empty($certificados)): ?>
                <p>No hay certificados registrados<?= $instructorIdFiltro ? ' para este instructor' : ''; ?>.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Entidad</th>
                                <th>Instructor</th>
                                <th>Imagen</th>
                                <?php if ($puedeCrearCertificados): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificados as $cert): ?>
                                <?php
                                $instructorCert = null;
                                foreach ($instructores as $instructor) {
                                    if ($instructor->getInstructorId() == $cert->getIdInstructor()) {
                                        $instructorCert = $instructor;
                                        break;
                                    }
                                }

                                $imagenCertificado = null;
                                $imageId = $cert->getTbcertificadoImagenId();

                                if (!empty($imageId)) {
                                    $imagen = $imageManager->getImagesByIds($imageId);

                                    if (!empty($imagen) && !empty($imagen[0]['tbimagenruta'])) {
                                        $imagePath = '..' . htmlspecialchars($imagen[0]['tbimagenruta']);
                                        $imagenCertificado = $imagePath . '?t=' . time();
                                    }
                                }
                                ?>
                                <tr>
                                    <form method="post" action="../action/certificadoAction.php" enctype="multipart/form-data" onsubmit="return validateCertificadoEditForm(this)">
                                        <td>
                                            <input type="hidden" name="id" value="<?= $cert->getId(); ?>">
                                            <?= str_pad($cert->getId(), 3, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td><input type="text" name="nombre" value="<?= htmlspecialchars($cert->getNombre()); ?>" required></td>
                                        <td><input type="text" name="descripcion" value="<?= htmlspecialchars($cert->getDescripcion()); ?>" required></td>
                                        <td><input type="text" name="entidad" value="<?= htmlspecialchars($cert->getEntidad()); ?>" required></td>
                                        <td>
                                            <select name="idInstructor" required>
                                                <option value="">Seleccione un instructor</option>
                                                <?php foreach ($instructores as $instructor): ?>
                                                    <option value="<?= $instructor->getInstructorId(); ?>" <?= $instructor->getInstructorId() == $cert->getIdInstructor() ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($instructor->getInstructorNombre() . ' (' . str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT) . ')'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <?php if ($imagenCertificado): ?>
                                                <div class="image-container">
                                                    <img src="<?= $imagenCertificado; ?>" alt="Imagen certificado">
                                                    <?php if ($puedeCrearCertificados): ?>
                                                        <button type="button" data-certificado-id="<?= $cert->getId(); ?>" data-image-id="<?= $cert->getTbcertificadoImagenId(); ?>" class="delete-image-btn" onclick="confirmImageDeleteCert(this)">
                                                            <i class="ph ph-x"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php elseif ($puedeCrearCertificados): ?>
                                                <input type="file" name="tbcertificadoimagenid[]" accept="image/png, image/jpeg, image/webp">
                                            <?php else: ?>
                                                <span class="text-muted">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($puedeCrearCertificados): ?>
                                            <td>
                                                <div class="actions">
                                                    <button type="submit" name="update" class="btn-row"><i class="ph ph-floppy-disk"></i></button>
                                                    <button type="submit" name="delete" class="btn-row btn-danger" onclick="return confirm('¿Eliminar este certificado?');"><i class="ph ph-trash"></i></button>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
<script>
// Validación en tiempo real para el formulario de creación
document.addEventListener('DOMContentLoaded', function () {
    const nombre = document.getElementById('nombre');
    const descripcion = document.getElementById('descripcion');
    const entidad = document.getElementById('entidad');
    const idInstructor = document.getElementById('idInstructor');

    // Mensajes de error
    const errorMessages = {
        nombreLongitud: 'El nombre no puede tener más de 100 caracteres.',
        descripcionVacia: 'La descripción no puede estar vacía.',
        entidadVacia: 'La entidad no puede estar vacía.',
        instructorNoSeleccionado: 'Debe seleccionar un instructor.'
    };

    // Validación en tiempo real
    if (nombre) nombre.addEventListener('blur', validarNombre);
    if (descripcion) descripcion.addEventListener('blur', validarDescripcion);
    if (entidad) entidad.addEventListener('blur', validarEntidad);
    if (idInstructor) idInstructor.addEventListener('blur', validarInstructor);

    function validarNombre() {
        const value = nombre.value.trim();

        if (value.length > 100) {
            showError(nombre, errorMessages.nombreLongitud);
            return false;
        }

        hideError(nombre);
        return true;
    }

    function validarDescripcion() {
        const value = descripcion.value.trim();

        if (value === '') {
            showError(descripcion, errorMessages.descripcionVacia);
            return false;
        }

        hideError(descripcion);
        return true;
    }

    function validarEntidad() {
        const value = entidad.value.trim();

        if (value === '') {
            showError(entidad, errorMessages.entidadVacia);
            return false;
        }

        hideError(entidad);
        return true;
    }

    function validarInstructor() {
        const value = idInstructor.value;

        if (value === '') {
            showError(idInstructor, errorMessages.instructorNoSeleccionado);
            return false;
        }

        hideError(idInstructor);
        return true;
    }

    function showError(input, message) {
        // Remover error previo
        hideError(input);

        // Crear elemento de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;

        // Insertar después del input
        input.parentNode.appendChild(errorDiv);

        // Resaltar input
        input.classList.add('error');
    }

    function hideError(input) {
        // Remover mensaje de error
        const errorDiv = input.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }

        // Restaurar borde
        input.classList.remove('error');
    }
});

// Validación antes de enviar el formulario de creación
function validateCertificadoForm() {
    const nombre = document.getElementById('nombre');
    const descripcion = document.getElementById('descripcion');
    const entidad = document.getElementById('entidad');
    const idInstructor = document.getElementById('idInstructor');

    let isValid = true;

    // Validar nombre
    if (nombre.value.trim().length > 100) {
        alert('El nombre no puede tener más de 100 caracteres.');
        nombre.focus();
        return false;
    }

    // Validar descripción
    if (descripcion.value.trim() === '') {
        alert('La descripción no puede estar vacía.');
        descripcion.focus();
        return false;
    }

    // Validar entidad
    if (entidad.value.trim() === '') {
        alert('La entidad no puede estar vacía.');
        entidad.focus();
        return false;
    }

    // Validar instructor
    if (idInstructor.value === '') {
        alert('Debe seleccionar un instructor.');
        idInstructor.focus();
        return false;
    }

    return true;
}

// Validación para formularios de edición
function validateCertificadoEditForm(form) {
    const nombre = form.querySelector('input[name="nombre"]');
    const descripcion = form.querySelector('input[name="descripcion"]');
    const entidad = form.querySelector('input[name="entidad"]');
    const idInstructor = form.querySelector('select[name="idInstructor"]');

    // Validar nombre
    if (nombre.value.trim().length > 100) {
        alert('El nombre no puede tener más de 100 caracteres.');
        nombre.focus();
        return false;
    }

    // Validar descripción
    if (descripcion.value.trim() === '') {
        alert('La descripción no puede estar vacía.');
        descripcion.focus();
        return false;
    }

    // Validar entidad
    if (entidad.value.trim() === '') {
        alert('La entidad no puede estar vacía.');
        entidad.focus();
        return false;
    }

    // Validar instructor
    if (idInstructor.value === '') {
        alert('Debe seleccionar un instructor.');
        idInstructor.focus();
        return false;
    }

    return true;
}

function confirmImageDeleteCert(button) {
    if (confirm('¿Estás seguro de eliminar esta imagen?')) {
        const certificadoId = button.getAttribute('data-certificado-id');
        const imageId = button.getAttribute('data-image-id');

        // Crear formulario temporal
        const form = document.createElement('form');
        form.method = 'post';
        form.action = '../action/certificadoAction.php';

        // Agregar campos ocultos
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = certificadoId;
        form.appendChild(idInput);

        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_image';
        deleteInput.value = imageId;
        form.appendChild(deleteInput);

        // Enviar formulario
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>