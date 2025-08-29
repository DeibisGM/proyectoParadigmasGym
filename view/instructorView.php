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
    <style>
        .error { color: red; }
        .success { color: green; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 95%;
        }
        .id-cell {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .imagen-actual-container img {
            max-width: 100px;
            max-height: 100px;
            display: block;
            margin-bottom: 5px;
        }
        .eliminar-imagen-btn {
            cursor: pointer;
            background: #ff4444;
            color: white;
            border: none;
            padding: 3px 6px;
            border-radius: 3px;
        }
        .certificado-badge {
            margin: 2px;
            padding: 2px 5px;
            background: #007bff;
            color: white;
            border-radius: 3px;
            display: inline-block;
            font-size: 12px;
        }
        .btn-ver-certificados {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            display: inline-block;
        }
    .field-error {
        color: red;
        font-size: 12px;
        margin-top: 5px;
        font-weight: bold;
    }

    input.error {
        border-color: red;
        background-color: #ffe6e6;
    }
    </style>
</head>
<body>

    <header>
        <h2>Gimnasio - Instructores</h2>
        <a href="../index.php">Volver al Inicio</a>
    </header>

    <hr>

    <main>
        <?php if ($esAdmin): ?>
        <h2>Crear Nuevo Instructor</h2>
        <form method="post" action="../action/instructorAction.php" enctype="multipart/form-data" onsubmit="return validateForm()">
            <table border="1">
                <tr>
                    <th>Cédula (3 dígitos)</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Correo</th>
                    <th>Cuenta Bancaria</th>
                    <th>Contraseña</th>
                    <th>Imagen</th>
                    <th>Acción</th>
                </tr>
                <tr>
                    <td><input type="text" name="id" placeholder="Ej: 001" required pattern="[0-9]{3}" title="3 dígitos numéricos (001, 002, etc.)"></td>
                    <td><input type="text" name="nombre" placeholder="Nombre completo" required></td>
                    <td><input type="text" name="telefono" placeholder="Teléfono"></td>
                    <td><input type="text" name="direccion" placeholder="Dirección"></td>
                    <td><input type="email" name="correo" placeholder="correo@ejemplo.com" required></td>
                    <td><input type="text" name="cuenta" placeholder="Cuenta bancaria"></td>
                    <td><input type="password" name="contraseña" placeholder="Contraseña (4-8 chars)" required></td>
                    <td><input type="file" name="imagen" accept="image/png, image/jpeg, image/webp"></td>
                    <td><input type="submit" value="Crear" name="create"></td>
                </tr>
            </table>
        </form>
        <?php endif; ?>

        <h2><?php echo $esAdmin ? 'Lista de Instructores' : 'Nuestros Instructores'; ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Cedula</th>
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
                    <th>Ver Certificados</th>
                    <?php if ($esAdmin): ?>
                    <th>Estado</th>
                    <th>Acciones</th>
                    <?php elseif ($esInstructor): ?>
                    <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($instructores)) {
                    $colspan = $esAdmin ? 12 : ($esInstructor ? 8 : 7);
                    echo "<tr><td colspan='{$colspan}'>No hay instructores registrados</td></tr>";
                } else {
                    foreach ($instructores as $instructor) {
                        $puedeEditar = $esAdmin || $esInstructor;
                        echo '<tr>';
                        // MOSTRAR LA CÉDULA (ID) - siempre visible con formato de 3 dígitos
                        $instructorIdFormatted = str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT);
                        echo '<td class="id-cell">' . htmlspecialchars($instructorIdFormatted) . '</td>';

                        if ($puedeEditar) {
                            echo '<form method="post" action="../action/instructorAction.php" enctype="multipart/form-data">';
                            echo '<input type="hidden" name="id" value="' . $instructor->getInstructorId() . '">';

                            echo '<td><input type="text" name="nombre" value="' . htmlspecialchars($instructor->getInstructorNombre() ?? '') . '" required></td>';
                            echo '<td><input type="text" name="telefono" value="' . htmlspecialchars($instructor->getInstructorTelefono() ?? '') . '"></td>';
                            echo '<td><input type="text" name="direccion" value="' . htmlspecialchars($instructor->getInstructorDireccion() ?? '') . '"></td>';
                            echo '<td><input type="email" name="correo" value="' . htmlspecialchars($instructor->getInstructorCorreo() ?? '') . '" required></td>';

                            if ($esAdmin) {
                                echo '<td><input type="text" name="cuenta" value="' . htmlspecialchars($instructor->getInstructorCuenta() ?? '') . '"></td>';
                                echo '<td><input type="password" name="contraseña" value="' . htmlspecialchars($instructor->getInstructorContraseña() ?? '') . '" required></td>';
                                echo '<td data-image-manager>';
                                $nombreImagen = 'instructores_' . $instructor->getInstructorId() . '.jpg';
                                $rutaImagen = '../img/instructores/' . $nombreImagen;

                                if (file_exists($rutaImagen)) {
                                    echo '<div class="imagen-actual-container">';
                                    echo '<img src="' . $rutaImagen . '?t=' . time() . '" alt="Imagen actual">';
                                    echo '<button type="button" class="eliminar-imagen-btn">X</button>';
                                    echo '</div>';
                                    echo '<div class="input-imagen-container" style="display: none;">';
                                    echo '<input type="file" name="imagen" accept="image/png, image/jpeg, image/webp">';
                                    echo '</div>';
                                } else {
                                    echo '<div class="imagen-actual-container" style="display: none;">';
                                    echo '</div>';
                                    echo '<div class="input-imagen-container">';
                                    echo '<input type="file" name="imagen" accept="image/png, image/jpeg, image/webp">';
                                    echo '</div>';
                                }
                                echo '<input type="hidden" name="eliminar_imagen" value="0">';
                                echo '</td>';
                            }
                        } else {
                            // PARA USUARIOS NO ADMIN Y NO ES SU PROPIO PERFIL
                            echo '<td>' . htmlspecialchars($instructor->getInstructorNombre() ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($instructor->getInstructorTelefono() ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($instructor->getInstructorDireccion() ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($instructor->getInstructorCorreo() ?? '') . '</td>';
                        }

                        // COLUMNA DE CERTIFICADOS (para todos los usuarios)
                        echo '<td>';
                        $certificadosInstructor = $certificadoBusiness->getCertificadosPorInstructor($instructor->getInstructorId());

                        if (!empty($certificadosInstructor)) {
                            $certificadosIds = [];
                            foreach ($certificadosInstructor as $cert) {
                                $certificadosIds[] = str_pad($cert->getId(), 3, '0', STR_PAD_LEFT);
                            }
                            echo implode(' | ', $certificadosIds);
                        } else {
                            echo 'Sin certificados';
                        }
                        echo '</td>';

                        // BOTÓN VER CERTIFICADOS (siempre visible)
                        echo '<td>';
                        echo '<a href="../view/certificadoView.php?instructor_id=' . $instructor->getInstructorId() . '" class="btn-ver-certificados">Ver Certificados</a>';
                        echo '</td>';

                        if ($puedeEditar) {
                            if ($esAdmin) {
                                echo '<td>' . ($instructor->getInstructorActivo() ? 'Activo' : 'Inactivo') . '</td>';
                            }

                            echo '<td>
                                    <input type="submit" value="Actualizar" name="update">';
                            if ($esAdmin) {
                                echo '<input type="submit" value="Eliminar" name="delete" onclick="return confirm(\'¿Eliminar instructor?\')">';
                                if (!$instructor->getInstructorActivo()) {
                                    echo '<input type="submit" value="Activar" name="activate">';
                                }
                            }
                            echo '</td>';

                            if ($puedeEditar) {
                                echo '</form>';
                            }
                        }
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>

        <div>
            <?php
            if (isset($_GET['error'])) {
                echo '<p class="error"><b>';
                if ($_GET['error'] == "emptyFields") {
                    echo 'Error: Los campos obligatorios no pueden estar vacíos.';
                } else if ($_GET['error'] == "invalidName") {
                    echo 'Error: El nombre no puede contener números.';
                } else if ($_GET['error'] == "nameTooLong") {
                    echo 'Error: El nombre es demasiado largo.';
                } else if ($_GET['error'] == "invalidEmail") {
                    echo 'Error: El correo electrónico no es válido.';
                } else if ($_GET['error'] == "dbError") {
                    echo 'Error: No se pudo procesar la transacción en la base de datos.';
                } else if ($_GET['error'] == "passwordLengthInvalid") {
                    echo 'Error: La contraseña debe tener entre 4 y 8 caracteres.';
                } else if ($_GET['error'] == "invalidId") {
                    echo 'Error: La cédula debe contener exactamente 3 dígitos numéricos.';
                } else if ($_GET['error'] == "idExists") {
                    echo 'Error: La cédula ya está registrada para otro instructor.';
                } else if ($_GET['error'] == "emailExists") {
                    echo 'Error: El correo electrónico ya está registrado para otro instructor.';
                } else if ($_GET['error'] == "error") {
                    echo 'Error: Ocurrió un error inesperado.';
                }
                else if ($_GET['error'] == "invalidPhone") {
                echo 'Error: El teléfono solo puede contener números.';
                } else if ($_GET['error'] == "phoneLengthInvalid") {
                echo 'Error: El teléfono debe tener entre 8 y 15 dígitos.';
                }
                echo '</b></p>';
            } else if (isset($_GET['success'])) {
                echo '<p class="success"><b>';
                if ($_GET['success'] == "created") {
                    echo 'Éxito: Instructor creado correctamente.';
                } else if ($_GET['success'] == "updated") {
                    echo 'Éxito: Instructor actualizado correctamente.';
                } else if ($_GET['success'] == "deleted") {
                    echo 'Éxito: Instructor eliminado correctamente.';
                } else if ($_GET['success'] == "activated") {
                    echo 'Éxito: Instructor activado correctamente.';
                }
                echo '</b></p>';
            }
            ?>
        </div>
    </main>

    <hr>

    <footer>
        <p>Fin de la página.</p>
    </footer>

   <script>
   // Validación en tiempo real para todos los campos
   document.addEventListener('DOMContentLoaded', function() {
       // Elementos del formulario
       const form = document.querySelector('form');
       const cedula = document.querySelector('input[name="id"]');
       const nombre = document.querySelector('input[name="nombre"]');
       const telefono = document.querySelector('input[name="telefono"]');
       const correo = document.querySelector('input[name="correo"]');
       const cuenta = document.querySelector('input[name="cuenta"]');
       const contraseña = document.querySelector('input[name="contraseña"]');

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

       // Validación antes de enviar el formulario
       if (form) {
           form.addEventListener('submit', function(e) {
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
           telefono.addEventListener('input', function(e) {
               // Remover cualquier caracter que no sea número
               this.value = this.value.replace(/[^0-9]/g, '');
           });

           // También prevenir pegar texto no numérico
           telefono.addEventListener('paste', function(e) {
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

   // Gestión de imágenes (manteniendo tu código original)
   document.addEventListener('DOMContentLoaded', function () {
       // Delegación de eventos para los botónes 'X' y los inputs de archivo
       document.addEventListener('click', function (event) {
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

       document.addEventListener('change', function (event) {
           // Si se seleccionó un archivo en an input de imagen
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