
<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
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
        .btn-certificados {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
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
        <h2>Crear / Editar Instructores</h2>

        <table border="1">
            <thead>
                <tr>
                    <th>Cedula*</th>
                    <th>Nombre*</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Correo*</th>
                    <th>Cuenta Bancaria</th>
                    <th>Contraseña*</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <!-- Formulario para crear nuevo instructor -->
                <tr>
                    <form method="post" action="../action/instructorAction.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <td>
                            <input type="text" name="id" placeholder="Ej: 123456789" required 
                                   pattern="[0-9]{9,20}" title="Solo números, entre 9 y 20 dígitos" style="width: 95%;">
                        </td>
                        <td>
                            <input type="text" name="nombre" placeholder="Ej: Juan Pérez" required
                                   pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios" style="width: 95%;">
                        </td>
                        <td>
                            <input type="text" name="telefono" placeholder="Ej: 8888-8888" style="width: 95%;">
                        </td>
                        <td>
                            <input type="text" name="direccion" placeholder="Ej: San José, Costa Rica" style="width: 95%;">
                        </td>
                        <td>
                            <input type="email" name="correo" placeholder="Ej: juan@email.com" required style="width: 95%;">
                        </td>
                        <td>
                            <input type="text" name="cuenta" placeholder="Ej: CR05015202001026284066"
                                pattern="[A-Z]{2}\d{2}[\s\-]?[A-Z\d]{4}[\s\-]?[A-Z\d]{4}[\s\-]?[A-Z\d]{4}[\s\-]?[A-Z\d]{4}[\s\-]?[A-Z\d]{0,20}"
                                title="Formato IBAN: 2 letras (país) + 2 dígitos + hasta 30 caracteres alfanuméricos"
                                style="width: 95%;">
                        </td>
                        <td>
                            <input type="password" name="contraseña" placeholder="Ej: noelia123" required style="width: 95%;">
                        </td>
                        <td>
                            <input type="file" name="imagen" accept="image/png, image/jpeg, image/webp">
                        </td>
                        <td>
                            <input type="submit" value="Crear" name="create">
                        </td>
                    </form>
                </tr>
            </tbody>
        </table>
        <?php endif; ?>

        <h2>Lista de Instructores</h2>
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
                    <th>Estado</th>
                    <th>Acciones</th>
                    <?php else: ?>
                    <th>Certificados</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once '../business/instructorBusiness.php';

                $business = new InstructorBusiness();
                $instructores = $business->getAllTBInstructor($esAdmin);

                if (empty($instructores)) {
                    $colspan = $esAdmin ? 10 : 6;
                    echo "<tr><td colspan='{$colspan}'>No hay instructores registrados</td></tr>";
                } else {
                    foreach ($instructores as $instructor) {
                        echo '<tr>';
                        // MOSTRAR LA CÉDULA (ID) - siempre visible
                        echo '<td class="id-cell">' . htmlspecialchars($instructor->getInstructorId() ?? '') . '</td>';
                        
                        if ($esAdmin) {
                            echo '<form method="post" action="../action/instructorAction.php" enctype="multipart/form-data">';
                            echo '<input type="hidden" name="id" value="' . $instructor->getInstructorId() . '">';

                            echo '<td><input type="text" name="nombre" value="' . htmlspecialchars($instructor->getInstructorNombre() ?? '') . '" required></td>';
                            echo '<td><input type="text" name="telefono" value="' . htmlspecialchars($instructor->getInstructorTelefono() ?? '') . '"></td>';
                            echo '<td><input type="text" name="direccion" value="' . htmlspecialchars($instructor->getInstructorDireccion() ?? '') . '"></td>';
                            echo '<td><input type="email" name="correo" value="' . htmlspecialchars($instructor->getInstructorCorreo() ?? '') . '" required></td>';
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
                            
                            echo '<td>' . ($instructor->getInstructorActivo() ? 'Activo' : 'Inactivo') . '</td>';

                            echo '<td>
                                    <input type="submit" value="Actualizar" name="update">
                                    <input type="submit" value="Eliminar" name="delete" onclick="return confirm(\'¿Eliminar instructor?\')">
                                  ';
                            if (!$instructor->getInstructorActivo()) {
                                echo '<input type="submit" value="Activar" name="activate">';
                            }
                            echo '<a href="../view/certificadoView.php?instructor_id=' . $instructor->getInstructorId() . '" class="btn-certificados">Ver Certificados</a>';
                            echo '</td>';

                            echo '</form>';
                        } else {
                            echo '<td>' . htmlspecialchars($instructor->getInstructorNombre() ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($instructor->getInstructorTelefono() ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($instructor->getInstructorDireccion() ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($instructor->getInstructorCorreo() ?? '') . '</td>';
                            echo '<td>';
                            // FORMATO CORREGIDO para visualización de usuarios no admin
                            $nombreImagen = 'instructores_' . $instructor->getInstructorId() . '.jpg'; // Cambiado a "instructores"
                            $rutaImagen = '../img/instructores/' . $nombreImagen;
                            if (file_exists($rutaImagen)) {
                                echo '<img src="' . $rutaImagen . '?t=' . time() . '" alt="Imagen" style="max-width: 100px; max-height: 100px;">';
                            } else {
                                echo 'Sin imagen';
                            }
                            echo '</td>';
                            echo '<td>';
                            echo '<a href="../view/certificadoView.php?instructor_id=' . $instructor->getInstructorId() . '" class="btn-certificados">Ver Certificados</a>';
                            echo '</td>';
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
                    echo 'Error: La cédula solo debe contener números.';
                } else if ($_GET['error'] == "idLengthInvalid") {
                    echo 'Error: La cédula debe tener entre 9 y 20 dígitos.';
                } else if ($_GET['error'] == "invalidIBAN") {
                    echo 'Error: El número de cuenta IBAN no es válido. Debe seguir el formato estándar internacional.';
                } else if ($_GET['error'] == "error") {
                    echo 'Error: Ocurrió un error inesperado.';
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
       function validateForm() {
            const cedula = document.querySelector('input[name="id"]');
            const nombre = document.querySelector('input[name="nombre"]');
            const correo = document.querySelector('input[name="correo"]');
            const cuenta = document.querySelector('input[name="cuenta"]');
            const contraseña = document.querySelector('input[name="contraseña"]');

            // Validación de cédula (solo números)
            if (!cedula.value.match(/^[0-9]+$/)) {
                alert("La cédula solo debe contener números.");
                return false;
            }

            if (cedula.value.length < 9 || cedula.value.length > 20) {
                alert("La cédula debe tener entre 9 y 20 dígitos.");
                return false;
            }

            // Validación de nombre (solo letras)
            if (!nombre.value.match(/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/)) {
                alert("El nombre solo debe contener letras y espacios.");
                return false;
            }

            // Validación básica de correo
            if (!correo.value.match(/^[\S@]+\@[\S@]+\.[\S@]+$/)) {
                alert("Por favor ingrese un correo electrónico válido.");
                return false;
            }

            // Validación de contraseña
            if (contraseña.value.length < 4 || contraseña.value.length > 8) {
                alert("La contraseña debe tener entre 4 y 8 caracteres.");
                return false;
            }

            // Validación de IBAN
            if (cuenta.value && !validateIBAN(cuenta.value)) {
                alert("Por favor ingrese un IBAN válido (Ej: CR05015202001026284066).");
                return false;
            }

            return true;
        }

        function validateIBAN(iban) {
            iban = iban.replace(/\s+/g, '').toUpperCase();

            const ibanRegex = /^[A-Z]{2}\d{2}[A-Z\d]{1,30}$/;

            if (!ibanRegex.test(iban)) {
                return false;
            }

            return true;
        }

        // Gestión de imágenes 
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
                // Si se seleccionó un archivo en un input de imagen
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
