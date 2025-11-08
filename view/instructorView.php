<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
$esInstructor = ($_SESSION['tipo_usuario'] === 'instructor');
$esCliente = ($_SESSION['tipo_usuario'] === 'cliente' || $_SESSION['tipo_usuario'] === 'usuario');

// Si es instructor, solo puede ver/editar su propio perfil
$instructorIdSesion = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

// Incluir business de certificados
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
        <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <div class="title-group">
            <h2><i class="ph ph-chalkboard-teacher"></i>Gestión de Instructores</h2>
            <p class="title-subtitle"><?= $esAdmin ? 'Administra el equipo del gimnasio.' : 'Consulta la información del equipo disponible.' ?></p>
        </div>
    </header>

    <main>
        <?php if ($esAdmin): ?>
            <section>
                <h3><i class="ph ph-user-plus"></i>Registrar instructor</h3>
                <form method="post" action="../action/instructorAction.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="form-grid-container">
                        <div class="form-group">
                            <label for="id"><i class="ph ph-identification-card"></i>Cédula (3 dígitos)</label>
                            <input type="text" name="id" id="id" placeholder="Ej: 001" required pattern="[0-9]{3}" title="3 dígitos numéricos (001, 002, etc.)">
                        </div>
                        <div class="form-group">
                            <label for="nombre"><i class="ph ph-user"></i>Nombre completo</label>
                            <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono"><i class="ph ph-phone"></i>Teléfono</label>
                            <input type="text" name="telefono" id="telefono" placeholder="Teléfono">
                        </div>
                        <div class="form-group">
                            <label for="direccion"><i class="ph ph-map-pin"></i>Dirección</label>
                            <input type="text" name="direccion" id="direccion" placeholder="Dirección">
                        </div>
                        <div class="form-group">
                            <label for="correo"><i class="ph ph-envelope"></i>Correo</label>
                            <input type="email" name="correo" id="correo" placeholder="correo@ejemplo.com" required>
                        </div>
                        <div class="form-group">
                            <label for="cuenta"><i class="ph ph-bank"></i>Cuenta bancaria</label>
                            <input type="text" name="cuenta" id="cuenta" placeholder="IBAN o cuenta bancaria">
                        </div>
                        <div class="form-group">
                            <label for="contraseña"><i class="ph ph-lock"></i>Contraseña</label>
                            <input type="password" name="contraseña" id="contraseña" placeholder="Contraseña (4-8 chars)" required>
                        </div>
                        <div class="form-group">
                            <label for="verificar_contraseña"><i class="ph ph-lock-key"></i>Verificar contraseña</label>
                            <input type="password" name="verificar_contraseña" id="verificar_contraseña" placeholder="Repetir contraseña" required>
                        </div>
                        <div class="form-group form-group-horizontal">
                            <label for="tbinstructorimagenid"><i class="ph ph-image"></i>Imagen</label>
                            <input type="file" name="tbinstructorimagenid[]" id="tbinstructorimagenid" accept="image/png, image/jpeg, image/webp">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="create"><i class="ph ph-floppy-disk"></i>Crear instructor</button>
                    </div>
                </form>
                <div class="password-toggle" onclick="togglePasswordVisibility()"><i class="ph ph-eye"></i>Mostrar/Ocultar contraseñas</div>
            </section>
        <?php endif; ?>

        <?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
            <?php
            $errores = [
                'datos_faltantes' => 'Los campos obligatorios no pueden estar vacíos.',
                'invalidName' => 'El nombre no puede contener números.',
                'nameTooLong' => 'El nombre es demasiado largo.',
                'correo_invalido' => 'El correo electrónico no es válido.',
                'dbError' => 'No se pudo procesar la transacción en la base de datos.',
                'passwordLengthInvalid' => 'La contraseña debe tener entre 4 y 8 caracteres.',
                'invalidId' => 'La cédula debe contener exactamente 3 dígitos numéricos.',
                'existe' => 'La cédula ya está registrada para otro instructor.',
                'emailExists' => 'El correo electrónico ya está registrado para otro instructor.',
                'error' => 'Ocurrió un error inesperado.',
                'invalidPhone' => 'El teléfono solo puede contener números.',
                'phoneLengthInvalid' => 'El teléfono debe tener entre 8 y 15 dígitos.',
                'image_deleted' => 'No se pudo eliminar la imagen.',
                'password_mismatch' => 'Las contraseñas no coinciden.'
            ];
            $exitos = [
                'inserted' => 'Instructor creado correctamente.',
                'updated' => 'Instructor actualizado correctamente.',
                'eliminado' => 'Instructor eliminado correctamente.',
                'activated' => 'Instructor activado correctamente.',
                'image_deleted' => 'Imagen eliminada correctamente.'
            ];
            ?>
            <?php if (isset($_GET['error'])): ?>
                <?php $claveError = $_GET['error']; ?>
                <div class="error-message">
                    <i class="ph ph-warning-circle"></i>
                    <span><?= $errores[$claveError] ?? ('Error: ' . htmlspecialchars($claveError)); ?></span>
                </div>
            <?php elseif (isset($_GET['success'])): ?>
                <?php $claveSuccess = $_GET['success']; ?>
                <div class="success-message">
                    <i class="ph ph-check-circle"></i>
                    <span><?= $exitos[$claveSuccess] ?? 'Operación completada correctamente.'; ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-users-three"></i><?= $esAdmin ? 'Listado de instructores' : 'Equipo del gimnasio' ?></h3>
            <p class="section-subtitle">
                <?= $esAdmin ? 'Gestiona datos de contacto, accesos y certificaciones.' : 'Revisa los datos de contacto y certificaciones disponibles.' ?>
            </p>

            <?php if (empty($instructores)): ?>
                <p>No hay instructores registrados.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Correo</th>
                            <?php if ($esAdmin): ?>
                                <th>Cuenta bancaria</th>
                                <th>Contraseña</th>
                                <th>Confirmación</th>
                                <th>Imagen</th>
                            <?php endif; ?>
                            <th>Certificados</th>
                            <th>Ver certificados</th>
                            <?php if ($esAdmin): ?>
                                <th>Estado</th>
                                <th>Acciones</th>
                            <?php elseif ($esInstructor): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($instructores as $instructor): ?>
                            <?php
                            $puedeEditar = $esAdmin || $esInstructor;
                            $formId = 'instructor-form-' . $instructor->getInstructorId();
                            $instructorIdFormatted = str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT);
                            $certificadosInstructor = $certificadoBusiness->getCertificadosPorInstructor($instructor->getInstructorId());
                            $certificadosIds = [];
                            if (!empty($certificadosInstructor)) {
                                foreach ($certificadosInstructor as $cert) {
                                    $certificadosIds[] = str_pad($cert->getId(), 3, '0', STR_PAD_LEFT);
                                }
                            }
                            $imageId = $instructor->getTbinstructorImagenId();
                            $imagenInstructor = null;
                            if (!empty($imageId)) {
                                $imagen = $imageManager->getImagesByIds($imageId);
                                if (!empty($imagen) && !empty($imagen[0]['tbimagenruta'])) {
                                    $imagePath = '..' . htmlspecialchars($imagen[0]['tbimagenruta']);
                                    $imagenInstructor = $imagePath . '?t=' . time();
                                }
                            }
                            ?>
                            <form id="<?= $formId ?>" method="post" action="../action/instructorAction.php" enctype="multipart/form-data"></form>
                            <tr>
                                <td class="id-cell">
                                    <?= htmlspecialchars($instructorIdFormatted); ?>
                                    <?php if ($puedeEditar): ?>
                                        <input type="hidden" name="id" value="<?= $instructor->getInstructorId(); ?>" form="<?= $formId ?>">
                                    <?php endif; ?>
                                </td>
                                <?php if ($puedeEditar): ?>
                                    <td><input type="text" name="nombre" value="<?= htmlspecialchars($instructor->getInstructorNombre() ?? ''); ?>" required form="<?= $formId ?>"></td>
                                    <td><input type="text" name="telefono" value="<?= htmlspecialchars($instructor->getInstructorTelefono() ?? ''); ?>" form="<?= $formId ?>"></td>
                                    <td><input type="text" name="direccion" value="<?= htmlspecialchars($instructor->getInstructorDireccion() ?? ''); ?>" form="<?= $formId ?>"></td>
                                    <td><input type="email" name="correo" value="<?= htmlspecialchars($instructor->getInstructorCorreo() ?? ''); ?>" required form="<?= $formId ?>"></td>
                                    <?php if ($esAdmin): ?>
                                        <td><input type="text" name="cuenta" value="<?= htmlspecialchars($instructor->getInstructorCuenta() ?? ''); ?>" form="<?= $formId ?>"></td>
                                        <td><input type="password" name="contraseña" id="contraseña_<?= $instructor->getInstructorId(); ?>" value="<?= htmlspecialchars($instructor->getInstructorContraseña() ?? ''); ?>" required form="<?= $formId ?>"></td>
                                        <td><input type="password" name="verificar_contraseña" id="verificar_contraseña_<?= $instructor->getInstructorId(); ?>" placeholder="Repetir contraseña" required form="<?= $formId ?>"></td>
                                        <td>
                                            <?php if ($imagenInstructor): ?>
                                                <div class="image-container">
                                                    <img src="<?= $imagenInstructor; ?>" alt="Imagen del instructor">
                                                    <button type="button" class="delete-image-btn" data-instructor-id="<?= $instructor->getInstructorId(); ?>" data-image-id="<?= $imageId; ?>" onclick="confirmImageDelete(this)"><i class="ph ph-x"></i></button>
                                                </div>
                                            <?php else: ?>
                                                <input type="file" name="tbinstructorimagenid[]" accept="image/png, image/jpeg, image/webp" form="<?= $formId ?>">
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <td><?= htmlspecialchars($instructor->getInstructorNombre() ?? ''); ?></td>
                                    <td><?= htmlspecialchars($instructor->getInstructorTelefono() ?? ''); ?></td>
                                    <td><?= htmlspecialchars($instructor->getInstructorDireccion() ?? ''); ?></td>
                                    <td><?= htmlspecialchars($instructor->getInstructorCorreo() ?? ''); ?></td>
                                <?php endif; ?>

                                <td><?= empty($certificadosIds) ? 'Sin certificados' : implode(' | ', $certificadosIds); ?></td>
                                <td><a href="../view/certificadoView.php?instructor_id=<?= $instructor->getInstructorId(); ?>" class="btn-ver-certificados"><i class="ph ph-certificate"></i>Ver certificados</a></td>

                                <?php if ($esAdmin): ?>
                                    <td>
                                        <?php if ($instructor->getInstructorActivo()): ?>
                                            <span class="status-pill success"><i class="ph ph-check-circle"></i>Activo</span>
                                        <?php else: ?>
                                            <span class="status-pill error"><i class="ph ph-warning"></i>Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($puedeEditar): ?>
                                    <td>
                                    <div class="actions">
                                        <button type="submit" name="update" class="btn-row" form="<?= $formId ?>"><i class="ph ph-floppy-disk"></i></button>
                                        <?php if ($esAdmin): ?>
                                            <button type="submit" name="delete" class="btn-row btn-danger" form="<?= $formId ?>" onclick="return confirm('¿Eliminar este instructor?');"><i class="ph ph-trash"></i></button>
                                            <?php if (!$instructor->getInstructorActivo()): ?>
                                                <button type="submit" name="activate" class="btn-row" form="<?= $formId ?>"><i class="ph ph-lightning"></i></button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    </td>
                                <?php elseif ($esAdmin): ?>
                                    <td></td>
                                <?php endif; ?>
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
    // Validación en tiempo real para todos los campos
    document.addEventListener('DOMContentLoaded', function () {
        // Elementos del formulario
        const form = document.querySelector('form');
        const cedula = document.querySelector('input[name="id"]');
        const nombre = document.querySelector('input[name="nombre"]');
        const telefono = document.querySelector('input[name="telefono"]');
        const correo = document.querySelector('input[name="correo"]');
        const cuenta = document.querySelector('input[name="cuenta"]');
        const contraseña = document.querySelector('input[name="contraseña"]');
        const verificarContraseña = document.querySelector('input[name="verificar_contraseña"]');

        // Mensajes de error
        const errorMessages = {
            cedula: 'La cédula debe contener exactamente 3 dígitos numéricos (001, 002, etc.).',
            nombreNumeros: 'El nombre no puede contener números.',
            nombreLongitud: 'El nombre no puede tener más de 100 caracteres.',
            telefonoNumeros: 'El teléfono solo puede contener números.',
            telefonoLongitud: 'El teléfono debe tener entre 8 y 15 dígitos.',
            correoFormato: 'Por favor ingrese un correo electrónico válido.',
            correoUnico: 'Este correo electrónico ya está registrado.',
            contraseñaLongitud: 'La contraseña debe tener entre 4 y 8 caracteres.',
            contraseñasNoCoinciden: 'Las contraseñas no coinciden.',
            ibanInvalido: 'Por favor ingrese un IBAN válido (Ej: CR05015202001026284066).',
            cedulaUnica: 'Esta cédula ya está registrada.'
        };

        // Validación en tiempo real
        if (cedula) cedula.addEventListener('blur', validarCedula);
        if (nombre) nombre.addEventListener('blur', validarNombre);
        if (telefono) telefono.addEventListener('blur', validarTelefono);
        if (correo) correo.addEventListener('blur', validarCorreo);
        if (cuenta) cuenta.addEventListener('blur', validarCuenta);
        if (contraseña) contraseña.addEventListener('blur', validarContraseña);
        if (verificarContraseña) verificarContraseña.addEventListener('blur', validarVerificacionContraseña);

        // Validación antes de enviar el formulario
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });
        }

        function validarCedula() {
            const value = cedula.value.trim();
            if (!value.match(/^[0-9]{3}$/)) {
                showError(cedula, errorMessages.cedula);
                return false;
            }
            hideError(cedula);
            return true;
        }

        function validarNombre() {
            const value = nombre.value.trim();

            if (value.match(/[0-9]/)) {
                showError(nombre, errorMessages.nombreNumeros);
                return false;
            }

            if (value.length > 100) {
                showError(nombre, errorMessages.nombreLongitud);
                return false;
            }

            hideError(nombre);
            return true;
        }

        function validarTelefono() {
            const value = telefono.value.trim();

            // Validar que solo contenga números
            if (!value.match(/^[0-9]+$/)) {
                showError(telefono, errorMessages.telefonoNumeros);
                return false;
            }

            // Validar longitud (8-15 dígitos)
            if (value.length < 8 || value.length > 15) {
                showError(telefono, errorMessages.telefonoLongitud);
                return false;
            }

            hideError(telefono);
            return true;
        }

        function validarCorreo() {
            const value = correo.value.trim();

            if (!value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                showError(correo, errorMessages.correoFormato);
                return false;
            }

            hideError(correo);
            return true;
        }

        function validarContraseña() {
            const value = contraseña.value;

            if (value.length < 4 || value.length > 8) {
                showError(contraseña, errorMessages.contraseñaLongitud);
                return false;
            }

            hideError(contraseña);

            // Validar también la verificación si ya tiene valor
            if (verificarContraseña.value) {
                validarVerificacionContraseña();
            }

            return true;
        }

        function validarVerificacionContraseña() {
            if (contraseña.value !== verificarContraseña.value) {
                showError(verificarContraseña, errorMessages.contraseñasNoCoinciden);
                return false;
            }

            hideError(verificarContraseña);
            return true;
        }

        function validarCuenta() {
            const value = cuenta.value.trim();

            // Si el campo está vacío, es válido (opcional)
            if (value === '') {
                hideError(cuenta);
                return true;
            }

            if (!validateIBAN(value)) {
                showError(cuenta, errorMessages.ibanInvalido);
                return false;
            }

            hideError(cuenta);
            return true;
        }

        function validateForm() {
            let isValid = true;

            if (!validarCedula()) isValid = false;
            if (!validarNombre()) isValid = false;
            if (!validarTelefono()) isValid = false;
            if (!validarCorreo()) isValid = false;
            if (!validarContraseña()) isValid = false;
            if (!validarVerificacionContraseña()) isValid = false;
            if (!validarCuenta()) isValid = false;

            if (!isValid) {
                alert('Por favor corrija los errores en el formulario antes de enviar.');
            }

            return isValid;
        }

        function validateIBAN(iban) {
            iban = iban.replace(/\s+/g, '').toUpperCase();
            const ibanRegex = /^[A-Z]{2}\d{2}[A-Z\d]{1,30}$/;
            return ibanRegex.test(iban);
        }

        function showError(input, message) {
            // Remover error previo
            hideError(input);

            // Crear elemento de error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.style.color = 'red';
            errorDiv.style.fontSize = '12px';
            errorDiv.style.marginTop = '5px';
            errorDiv.textContent = message;

            // Insertar después del input
            input.parentNode.appendChild(errorDiv);

            // Resaltar input
            input.style.borderColor = 'red';
        }

        function hideError(input) {
            // Remover mensaje de error
            const errorDiv = input.parentNode.querySelector('.field-error');
            if (errorDiv) {
                errorDiv.remove();
            }

            // Restaurar borde
            input.style.borderColor = '';
        }

        // Prevenir que se ingresen caracteres no numéricos en el teléfono
        if (telefono) {
            telefono.addEventListener('input', function (e) {
                // Remover cualquier caracter que no sea número
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // También prevenir pegar texto no numérico
            telefono.addEventListener('paste', function (e) {
                const pastedData = e.clipboardData.getData('text');
                if (!/^[0-9]+$/.test(pastedData)) {
                    e.preventDefault();
                    alert('Solo se permiten números en el campo de teléfono');
                }
            });
        }
    });

    // Validación del formulario principal (para el onSubmit)
    function validateForm() {
        const cedula = document.querySelector('input[name="id"]');
        const nombre = document.querySelector('input[name="nombre"]');
        const telefono = document.querySelector('input[name="telefono"]');
        const correo = document.querySelector('input[name="correo"]');
        const cuenta = document.querySelector('input[name="cuenta"]');
        const contraseña = document.querySelector('input[name="contraseña"]');
        const verificarContraseña = document.querySelector('input[name="verificar_contraseña"]');

        // Validación de cédula (exactamente 3 dígitos)
        if (!cedula.value.match(/^[0-9]{3}$/)) {
            alert("La cédula debe contener exactamente 3 dígitos numéricos (001, 002, etc.).");
            cedula.focus();
            return false;
        }

        // Validación de nombre (solo letras y espacios)
        if (!nombre.value.match(/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/)) {
            alert("El nombre solo debe contener letras y espacios.");
            nombre.focus();
            return false;
        }

        // Validación de nombre (máximo 100 caracteres)
        if (nombre.value.length > 100) {
            alert("El nombre no puede tener más de 100 caracteres.");
            nombre.focus();
            return false;
        }

        // Validación de teléfono (solo números y longitud opcional)
        if (telefono.value && !telefono.value.match(/^[0-9]+$/)) {
            alert("El teléfono solo puede contener números.");
            telefono.focus();
            return false;
        }

        // Validación de teléfono (longitud 8-15 dígitos)
        if (telefono.value && (telefono.value.length < 8 || telefono.value.length > 15)) {
            alert("El teléfono debe tener entre 8 y 15 dígitos.");
            telefono.focus();
            return false;
        }

        // Validación básica de correo
        if (!correo.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            alert("Por favor ingrese un correo electrónico válido.");
            correo.focus();
            return false;
        }

        // Validación de contraseña (4-8 caracteres)
        if (contraseña.value.length < 4 || contraseña.value.length > 8) {
            alert("La contraseña debe tener entre 4 y 8 caracteres.");
            contraseña.focus();
            return false;
        }

        // Validación de coincidencia de contraseñas
        if (contraseña.value !== verificarContraseña.value) {
            alert("Las contraseñas no coinciden.");
            verificarContraseña.focus();
            return false;
        }

        // Validación de IBAN (si se ingresa)
        if (cuenta.value && !validateIBAN(cuenta.value)) {
            alert("Por favor ingrese un IBAN válido (Ej: CR05015202001026284066).");
            cuenta.focus();
            return false;
        }

        return true;
    }

    function validateIBAN(iban) {
        iban = iban.replace(/\s+/g, '').toUpperCase();
        const ibanRegex = /^[A-Z]{2}\d{2}[A-Z\d]{1,30}$/;
        return ibanRegex.test(iban);
    }

    // Función para mostrar/ocultar contraseñas
    function togglePasswordVisibility() {
        const contraseñaInputs = document.querySelectorAll('input[type="password"]');
        contraseñaInputs.forEach(input => {
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        });
    }
</script>

<script>
// Reemplaza todo el script de eliminación con este:
function confirmImageDelete(button) {
    if (confirm('¿Estás seguro de eliminar esta imagen?')) {
        const instructorId = button.getAttribute('data-instructor-id');
        const imageId = button.getAttribute('data-image-id');

        // Crear formulario temporal
        const form = document.createElement('form');
        form.method = 'post';
        form.action = '../action/instructorAction.php';

        // Agregar campos ocultos
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = instructorId;
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

// También asegúrate de que los botones de submit normales no interfieran
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-image-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });
});
</script>
</body>
</html>