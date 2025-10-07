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
$instructores = $instructorBusiness->getAllTBInstructor(true); // Obtener todos los instructores

// FILTRAR POR INSTRUCTOR SI SE ESPECIFICA
if (isset($_GET['instructor_id'])) {
    $instructorIdFiltro = $_GET['instructor_id'];
    $certificados = array_filter($certificados, function ($cert) use ($instructorIdFiltro) {
        return $cert->getIdInstructor() == $instructorIdFiltro;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Certificados</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background: #eee;
        }

        form {
            margin-bottom: 20px;
        }

        input, select {
            padding: 8px;
            margin: 5px 0;
            width: 200px;
            box-sizing: border-box;
        }

        button {
            padding: 8px 15px;
            cursor: pointer;
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        .delete-image-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: #ff4444;
            color: white;
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            line-height: 1;
        }

        .image-container img {
            max-width: 100px;
            max-height: 100px;
            display: block;
        }

        .field-error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            font-weight: bold;
        }

        input.error, select.error {
            border-color: red;
            background-color: #ffe6e6;
        }
    </style>
</head>
<body>
<header>
        <a href="instructorView.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2>Gestión de Certificados para el Instructor: <strong><?php echo htmlspecialchars($instructor_nombre); ?></strong></h2>
    </header>
<hr>

<main>
    <?php if ($puedeCrearCertificados): ?>
        <h2>Agregar Certificado</h2>
        <form method="post" action="../action/certificadoAction.php" enctype="multipart/form-data" onsubmit="return validateCertificadoForm()">
            <input type="text" name="nombre" id="nombre" placeholder="Nombre" required maxlength="100"><br>
            <input type="text" name="descripcion" id="descripcion" placeholder="Descripción" required><br>
            <input type="text" name="entidad" id="entidad" placeholder="Entidad" required><br>

            <label for="idInstructor">Instructor:</label><br>
            <select name="idInstructor" id="idInstructor" required>
                <option value="">Seleccione un instructor</option>
                <?php foreach ($instructores as $instructor): ?>
                    <option value="<?php echo $instructor->getInstructorId(); ?>">
                        <?php echo htmlspecialchars($instructor->getInstructorNombre() . ' (' . $instructor->getInstructorId() . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <!-- Agregar campo para imagen -->
            <label for="tbcertificadoimagenid">Imagen del certificado:</label><br>
            <input type="file" name="tbcertificadoimagenid[]" id="tbcertificadoimagenid" accept="image/png, image/jpeg, image/webp"><br>

            <button type="submit" name="create">Agregar</button>
        </form>
    <?php endif; ?>

    <h2>Lista de Certificados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Entidad</th>
            <th>Instructor</th>
            <th>Imagen</th>
            <?php if ($puedeCrearCertificados): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($certificados as $cert):
            // Obtener el nombre del instructor para este certificado
            $instructorCert = null;
            foreach ($instructores as $instructor) {
                if ($instructor->getInstructorId() == $cert->getIdInstructor()) {
                    $instructorCert = $instructor;
                    break;
                }
            }

            // Obtener la imagen del certificado
        $imagenCertificado = null;
        $imageId = $cert->getTbcertificadoImagenId();

        if (!empty($imageId)) {
            $imagen = $imageManager->getImagesByIds($imageId);

            // EXACTAMENTE IGUAL QUE EN INSTRUCTOR
            if (!empty($imagen) && !empty($imagen[0]['tbimagenruta'])) {
                $imagePath = '..' . htmlspecialchars($imagen[0]['tbimagenruta']);
                $imagenCertificado = $imagePath . '?t=' . time();
            }
        }
            ?>
            <tr>
                <form method="post" action="../action/certificadoAction.php" enctype="multipart/form-data" onsubmit="return validateCertificadoEditForm(this)">
                    <td>
                        <input type="hidden" name="id" value="<?php echo $cert->getId(); ?>">
                        <?php echo str_pad($cert->getId(), 3, '0', STR_PAD_LEFT); ?>
                    </td>
                    <td><input type="text" name="nombre" value="<?php echo htmlspecialchars($cert->getNombre()); ?>" required></td>
                    <td><input type="text" name="descripcion" value="<?php echo htmlspecialchars($cert->getDescripcion()); ?>" required></td>
                    <td><input type="text" name="entidad" value="<?php echo htmlspecialchars($cert->getEntidad()); ?>" required></td>
                    <td>
                        <select name="idInstructor" required>
                            <option value="">Seleccione un instructor</option>
                            <?php foreach ($instructores as $instructor): ?>
                                <option value="<?php echo $instructor->getInstructorId(); ?>" <?php echo $instructor->getInstructorId() == $cert->getIdInstructor() ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($instructor->getInstructorNombre() . ' (' . $instructor->getInstructorId() . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <?php if ($imagenCertificado): ?>
                          <div class="image-container">
                                 <img src="<?php echo $imagenCertificado; ?>" alt="Imagen certificado">
                                <?php if ($puedeCrearCertificados): ?>
                                    <button type="button" data-certificado-id="<?php echo $cert->getId(); ?>" data-image-id="<?php echo $cert->getTbcertificadoImagenId(); ?>" class="delete-image-btn" onclick="confirmImageDeleteCert(this)">X</button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php if ($puedeCrearCertificados): ?>
                                <input type="file" name="tbcertificadoimagenid[]" accept="image/png, image/jpeg, image/webp">
                            <?php else: ?>
                                Sin imagen
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <?php if ($puedeCrearCertificados): ?>
                        <td>
                            <button type="submit" name="update">Actualizar</button>
                            <button type="submit" name="delete" onclick="return confirm('¿Eliminar este certificado?');">Eliminar</button>
                        </td>
                    <?php endif; ?>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if (isset($_GET['success'])): ?>
        <p style="color:green;">
            <?php
            if ($_GET['success'] == 'created') echo 'Certificado creado correctamente.';
            elseif ($_GET['success'] == 'updated') echo 'Certificado actualizado correctamente.';
            elseif ($_GET['success'] == 'deleted') echo 'Certificado eliminado correctamente.';
            elseif ($_GET['success'] == 'image_deleted') echo 'Imagen eliminada correctamente.';
            ?>
        </p>
    <?php elseif (isset($_GET['error'])): ?>
        <p style="color:red;">
            <?php
            if ($_GET['error'] == 'emptyFields') echo 'Error: Todos los campos son obligatorios.';
            elseif ($_GET['error'] == 'nameTooLong') echo 'Error: El nombre es demasiado largo (máximo 100 caracteres).';
            elseif ($_GET['error'] == 'dbError') echo 'Error: No se pudo completar la operación en la base de datos.';
            elseif ($_GET['error'] == 'instructorNotFound') echo 'Error: Instructor no encontrado.';
            elseif ($_GET['error'] == 'notFound') echo 'Error: Certificado no encontrado.';
            elseif ($_GET['error'] == 'image_deleted') echo 'Error: No se pudo eliminar la imagen.';
            else echo 'Error: ' . htmlspecialchars($_GET['error']);
            ?>
        </p>
    <?php endif; ?>
</main>
<hr>
<footer>
    <p>Fin de la página.</p>
</footer>

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