<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
$esInstructor = ($_SESSION['tipo_usuario'] === 'instructor');
$esCliente = ($_SESSION['tipo_usuario'] === 'cliente' || $_SESSION['tipo_usuario'] === 'usuario');

$instructorIdSesion = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

include_once '../business/certificadoBusiness.php';
$certificadoBusiness = new CertificadoBusiness();
$todosCertificados = $certificadoBusiness->getCertificados();

include_once '../utility/ImageManager.php';
$imageManager = new ImageManager();

require_once '../business/instructorBusiness.php';
$business = new InstructorBusiness();
$instructores = $business->getAllTBInstructor($esAdmin);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Instructores</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>

    <div class="container">
        <header>
            <a href="../index.php" class="back-button">
                <i class="ph ph-arrow-left"></i>
            </a>
            <h2>Gestión de Instructores</h2>
        </header>

        <main>
            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">
                    <?php
                    switch ($_GET['error']) {
                        case 'datos_faltantes':
                            echo 'Error: Los campos obligatorios no pueden estar vacíos.';
                            break;
                        case 'invalidName':
                            echo 'Error: El nombre no puede contener números.';
                            break;
                        case 'nameTooLong':
                            echo 'Error: El nombre es demasiado largo.';
                            break;
                        case 'correo_invalido':
                            echo 'Error: El correo electrónico no es válido.';
                            break;
                        case 'dbError':
                            echo 'Error: No se pudo procesar la transacción en la base de datos.';
                            break;
                        case 'passwordLengthInvalid':
                            echo 'Error: La contraseña debe tener entre 4 y 8 caracteres.';
                            break;
                        case 'invalidId':
                            echo 'Error: La cédula debe contener exactamente 3 dígitos numéricos.';
                            break;
                        case 'existe':
                            echo 'Error: La cédula ya está registrada para otro instructor.';
                            break;
                        case 'emailExists':
                            echo 'Error: El correo electrónico ya está registrado para otro instructor.';
                            break;
                        case 'error':
                            echo 'Error: Ocurrió un error inesperado.';
                            break;
                        case 'invalidPhone':
                            echo 'Error: El teléfono solo puede contener números.';
                            break;
                        case 'phoneLengthInvalid':
                            echo 'Error: El teléfono debe tener entre 8 y 15 dígitos.';
                            break;
                        case 'image_deleted':
                            echo 'Error: No se pudo eliminar la imagen.';
                            break;
                        case 'password_mismatch':
                            echo 'Error: Las contraseñas no coinciden.';
                            break;
                        default:
                            echo 'Error: Ocurrió un error inesperado.';
                            break;
                    }
                    ?>
                </p>
            <?php elseif (isset($_GET['success'])): ?>
                <p class="success-message">
                    <?php
                    switch ($_GET['success']) {
                        case 'inserted':
                            echo 'Éxito: Instructor creado correctamente.';
                            break;
                        case 'updated':
                            echo 'Éxito: Instructor actualizado correctamente.';
                            break;
                        case 'eliminado':
                            echo 'Éxito: Instructor eliminado correctamente.';
                            break;
                        case 'activated':
                            echo 'Éxito: Instructor activado correctamente.';
                            break;
                        case 'image_deleted':
                            echo 'Éxito: Imagen eliminada correctamente.';
                            break;
                    }
                    ?>
                </p>
            <?php endif; ?>

            <?php if ($esAdmin): ?>
                <section>
                    <h3><i class="ph ph-user-plus"></i> Registrar Nuevo Instructor</h3>
                    <form method="post" action="../action/instructorAction.php" enctype="multipart/form-data">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="id">Cédula (3 dígitos):</label>
                                <input type="text" id="id" name="id" placeholder="Ej: 001" required
                                    pattern="[0-9]{3}" title="3 dígitos numéricos (001, 002, etc.)">
                            </div>
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" id="nombre" name="nombre" placeholder="Nombre completo" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono:</label>
                                <input type="text" id="telefono" name="telefono" placeholder="Teléfono">
                            </div>
                            <div class="form-group">
                                <label for="direccion">Dirección:</label>
                                <input type="text" id="direccion" name="direccion" placeholder="Dirección">
                            </div>
                            <div class="form-group">
                                <label for="correo">Correo:</label>
                                <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                            </div>
                            <div class="form-group">
                                <label for="cuenta">Cuenta Bancaria:</label>
                                <input type="text" id="cuenta" name="cuenta" placeholder="Cuenta bancaria">
                            </div>
                            <div class="form-group">
                                <label for="contraseña">Contraseña:</label>
                                <div class="password-group">
                                    <input type="password" id="contraseña" name="contraseña"
                                        placeholder="Contraseña (4-8 chars)" required>
                                    <i class="ph ph-eye" id="togglePassword"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="verificar_contraseña">Verificar Contraseña:</label>
                                <input type="password" id="verificar_contraseña" name="verificar_contraseña"
                                    placeholder="Repetir contraseña" required>
                            </div>
                            <div class="form-group form-group-horizontal">
                                <label for="tbinstructorimagenid">Foto de instructor:</label>
                                <input type="file" id="tbinstructorimagenid" name="tbinstructorimagenid[]"
                                    accept="image/png, image/jpeg, image/webp">
                            </div>
                        </div>
                        <button type="submit" name="create"><i class="ph ph-plus"></i> Crear Instructor</button>
                    </form>
                </section>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-list-bullets"></i>
                    <?php echo $esAdmin ? 'Lista de Instructores' : 'Nuestros Instructores'; ?>
                </h3>
                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Correo</th>
                                <?php if ($esAdmin): ?>
                                    <th>Cuenta Bancaria</th>
                                    <th>Contraseña</th>
                                    <th>Imagen</th>
                                <?php endif; ?>
                                <th>Certificados</th>
                                <?php if ($esAdmin): ?>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                <?php elseif ($esInstructor): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($instructores)): ?>
                                <tr>
                                    <td colspan="<?php echo $esAdmin ? 11 : 7; ?>">No hay instructores registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($instructores as $instructor): ?>
                                    <?php $puedeEditar = $esAdmin || ($esInstructor && $instructor->getInstructorId() == $instructorIdSesion); ?>
                                    <tr>
                                        <form id="form-<?= $instructor->getInstructorId() ?>" method="post"
                                            action="../action/instructorAction.php" enctype="multipart/form-data"></form>
                                        <input type="hidden" name="id" value="<?= $instructor->getInstructorId() ?>"
                                            form="form-<?= $instructor->getInstructorId() ?>">

                                        <td data-label="Cédula">
                                            <?php echo str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td data-label="Nombre">
                                            <input type="text" name="nombre"
                                                value="<?php echo htmlspecialchars($instructor->getInstructorNombre() ?? ''); ?>"
                                                <?php echo $puedeEditar ? '' : 'readonly'; ?> required
                                                form="form-<?= $instructor->getInstructorId() ?>">
                                        </td>
                                        <td data-label="Teléfono">
                                            <input type="text" name="telefono"
                                                value="<?php echo htmlspecialchars($instructor->getInstructorTelefono() ?? ''); ?>"
                                                <?php echo $puedeEditar ? '' : 'readonly'; ?>
                                                form="form-<?= $instructor->getInstructorId() ?>">
                                        </td>
                                        <td data-label="Dirección">
                                            <input type="text" name="direccion"
                                                value="<?php echo htmlspecialchars($instructor->getInstructorDireccion() ?? ''); ?>"
                                                <?php echo $puedeEditar ? '' : 'readonly'; ?>
                                                form="form-<?= $instructor->getInstructorId() ?>">
                                        </td>
                                        <td data-label="Correo">
                                            <input type="email" name="correo"
                                                value="<?php echo htmlspecialchars($instructor->getInstructorCorreo() ?? ''); ?>"
                                                <?php echo $puedeEditar ? '' : 'readonly'; ?> required
                                                form="form-<?= $instructor->getInstructorId() ?>">
                                        </td>

                                        <?php if ($esAdmin): ?>
                                            <td data-label="Cuenta Bancaria">
                                                <input type="text" name="cuenta"
                                                    value="<?php echo htmlspecialchars($instructor->getInstructorCuenta() ?? ''); ?>"
                                                    form="form-<?= $instructor->getInstructorId() ?>">
                                            </td>
                                            <td data-label="Contraseña">
                                                <input type="password" name="contraseña"
                                                    value="<?php echo htmlspecialchars($instructor->getInstructorContraseña() ?? ''); ?>"
                                                    required form="form-<?= $instructor->getInstructorId() ?>">
                                            </td>
                                            <td data-label="Imagen">
                                                <?php
                                                $imageId = $instructor->getTbinstructorImagenId();
                                                if (!empty($imageId)) {
                                                    $imagen = $imageManager->getImagesByIds($imageId);
                                                    if (!empty($imagen) && !empty($imagen[0]['tbimagenruta'])) {
                                                        $imagePath = '..' . htmlspecialchars($imagen[0]['tbimagenruta']);
                                                        $finalSrc = $imagePath . '?t=' . time();
                                                        echo '<div class="image-container"><img src="' . $finalSrc . '" alt="Imagen" onerror="this.onerror=null; this.src=\'noimage.svg\';"><button type="submit" name="delete_image" value="' . $imageId . '" onclick="return confirm(\'¿Eliminar esta imagen?\');" form="form-' . $instructor->getInstructorId() . '"><i class="ph ph-x"></i></button></div>';
                                                    } else {
                                                        echo '<input type="file" name="tbinstructorimagenid[]" accept="image/png, image/jpeg, image/webp" form="form-' . $instructor->getInstructorId() . '">';
                                                    }
                                                } else {
                                                    echo '<input type="file" name="tbinstructorimagenid[]" accept="image/png, image/jpeg, image/webp" form="form-' . $instructor->getInstructorId() . '">';
                                                }
                                                ?>
                                            </td>
                                        <?php endif; ?>

                                        <td data-label="Certificados">
                                            <a href="../view/certificadoView.php?instructor_id=<?php echo $instructor->getInstructorId(); ?>"
                                                class="btn-row"><i class="ph ph-file-text"></i></a>
                                        </td>

                                        <?php if ($esAdmin): ?>
                                            <td data-label="Estado">
                                                <select name="activo" form="form-<?= $instructor->getInstructorId() ?>">
                                                    <option value="1" <?php echo $instructor->getInstructorActivo() ? 'selected' : ''; ?>>
                                                        Activo</option>
                                                    <option value="0" <?php echo !$instructor->getInstructorActivo() ? 'selected' : ''; ?>>
                                                        Inactivo</option>
                                                </select>
                                            </td>
                                        <?php endif; ?>

                                        <?php if ($puedeEditar): ?>
                                            <td data-label="Acciones">
                                                <div class="actions">
                                                    <button type="submit" name="update"
                                                        form="form-<?= $instructor->getInstructorId() ?>" class="btn-row"><i
                                                            class="ph ph-pencil-simple"></i></button>
                                                    <?php if ($esAdmin): ?>
                                                        <button type="submit" name="delete"
                                                            onclick="return confirm('¿Eliminar instructor?')"
                                                            form="form-<?= $instructor->getInstructorId() ?>"
                                                            class="btn-row btn-danger"><i class="ph ph-trash"></i></button>
                                                    <?php endif; ?>
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

        <footer>
            <p>&copy;
                <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.
            </p>
        </footer>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('#togglePassword');
            if (togglePassword) {
                togglePassword.addEventListener('click', function (e) {
                    const password = document.querySelector('#contraseña');
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.classList.toggle('ph-eye-slash');
                });
            }
        });
    </script>
</body>

</html>