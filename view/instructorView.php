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
        input[type="text"], input[type="email"] {
            width: 95%;
        }
    </style>
</head>
<body>

    <header>
        <h2>Gimnasio - Instructores</h2>
    </header>

    <hr>

    <main>
        <h2>Crear / Editar Instructores</h2>

        <table border="1">
            <thead>
                <tr>
                    <th>Nombre*</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Correo*</th>
                    <th>Cuenta Bancaria</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <!-- Formulario para crear nuevo instructor -->
                <tr>
                    <form method="post" action="../action/instructorAction.php" onsubmit="return validateForm()">
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
                            <input type="text" name="cuenta" placeholder="Ej: ESXX XXXX XXXX XX" style="width: 95%;">
                        </td>
                        <td>
                            <input type="submit" value="Crear" name="create">
                        </td>
                    </form>
                </tr>

                <?php
// Verifica que el business esté incluido correctamente
require_once '../business/instructorBusiness.php';

// Crea instancia y obtiene datos
$business = new InstructorBusiness();
$instructores = $business->getAllTBInstructor();

if (empty($instructores)) {
    echo "<tr><td colspan='6'>No hay instructores registrados</td></tr>";
} else {
    foreach ($instructores as $instructor) {
        echo '<tr>';
        echo '<form method="post" action="../action/instructorAction.php">';
        echo '<input type="hidden" name="id" value="'.$instructor->getInstructorId().'">';

        echo '<td><input type="text" name="nombre" value="'.htmlspecialchars($instructor->getInstructorNombre()).'" required></td>';
        echo '<td><input type="text" name="telefono" value="'.htmlspecialchars($instructor->getInstructorTelefono()).'"></td>';
        echo '<td><input type="text" name="direccion" value="'.htmlspecialchars($instructor->getInstructorDireccion()).'"></td>';
        echo '<td><input type="email" name="correo" value="'.htmlspecialchars($instructor->getInstructorCorreo()).'" required></td>';
        echo '<td><input type="text" name="cuenta" value="'.htmlspecialchars($instructor->getInstructorCuenta()).'"></td>';

        echo '<td>
                <input type="submit" value="Actualizar" name="update">
                <input type="submit" value="Eliminar" name="delete" onclick="return confirm(\'¿Eliminar instructor?\')">
              </td>';

        echo '</form>';
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
            const nombre = document.querySelector('input[name="nombre"]');
            const correo = document.querySelector('input[name="correo"]');

            // Validación de nombre (solo letras)
            if (!nombre.value.match(/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/)) {
                alert("El nombre solo debe contener letras y espacios.");
                return false;
            }

            // Validación básica de correo
            if (!correo.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                alert("Por favor ingrese un correo electrónico válido.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>