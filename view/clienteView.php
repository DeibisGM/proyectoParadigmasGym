<?php
include '../business/clienteBusiness.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <script>
        function validarFormulario() {
            const nombre = document.forms["clienteForm"]["nombre"].value;
            const carnet = document.forms["clienteForm"]["carnet"].value;
            const telefono = document.forms["clienteForm"]["telefono"].value;
            const regexNombre = /^[a-zA-Z\s]+$/;
            const regexTelefono = /^\d{1,8}$/;

            if (!regexNombre.test(nombre)) {
                alert("El nombre solo puede contener letras.");
                return false;
            }
            if (!regexTelefono.test(telefono)) {
                alert("El teléfono debe tener solo números (máximo 8 dígitos).");
                return false;
            }
            return confirm('¿Estás seguro de que deseas crear este cliente?');
        }
    </script>
</head>
<body>

    <header>
        <h2>Gimnasio - Clientes</h2>
    </header>

    <hr>

    <main>
        <h2>Registrar Cliente</h2>

        <form name="clienteForm" method="post" action="../action/clienteAction.php" onsubmit="return validarFormulario();">
            <label>Carnet:</label><br>
            <input type="text" name="carnet" required><br>

            <label>Nombre:</label><br>
            <input type="text" name="nombre" required><br>

            <label>Fecha de nacimiento:</label><br>
            <input type="date" name="fechaNacimiento" required><br>

            <label>Teléfono:</label><br>
            <input type="text" name="telefono" maxlength="8" required><br>

            <label>Correo (opcional):</label><br>
            <input type="email" name="correo"><br>

            <label>Dirección:</label><br>
            <input type="text" name="direccion" required><br>

            <label>Género:</label><br>
            <select name="genero" required>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="Otro">Otro</option>
            </select><br>

            <label>Fecha de inscripción:</label><br>
            <input type="date" name="inscripcion" required><br><br>

            <input type="submit" value="Registrar Cliente" name="create">
        </form>

        <br><br>

        <h2>Clientes Registrados</h2>

        <table border="1" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Carnet</th>
                    <th>Fecha Nacimiento</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Género</th>
                    <th>Inscripción</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $clienteBusiness = new ClienteBusiness();
                $clientes = $clienteBusiness->getAllTBCliente();
                foreach ($clientes as $cliente) {
                    echo '<tr>';
                    echo '<form method="post" action="../action/clienteAction.php">';
                    echo '<input type="hidden" name="id" value="' . $cliente->getId() . '">';
                    echo '<td><input type="text" name="nombre" value="' . $cliente->getNombre() . '" required></td>';
                    echo '<td>' . $cliente->getCarnet() . '</td>';
                    echo '<td><input type="date" name="fechaNacimiento" value="' . $cliente->getFechaNacimiento() . '" required></td>';
                    echo '<td><input type="text" name="telefono" value="' . $cliente->getTelefono() . '" maxlength="8" required></td>';
                    echo '<td><input type="email" name="correo" value="' . $cliente->getCorreo() . '"></td>';
                    echo '<td><input type="text" name="direccion" value="' . $cliente->getDireccion() . '" required></td>';
                    echo '<td><select name="genero" required>';
                    echo '<option ' . ($cliente->getGenero() == 'M' ? 'selected' : '') . '>Masculino</option>';
                    echo '<option ' . ($cliente->getGenero() == 'F' ? 'selected' : '') . '>Femenino</option>';
                    echo '<option ' . ($cliente->getGenero() == 'Otro' ? 'selected' : '') . '>Otro</option>';
                    echo '</select></td>';
                    echo '<td><input type="date" name="inscripcion" value="' . $cliente->getInscripcion() . '" required></td>';
                    echo '<td>' . $cliente->getEstado() . '</td>';
                    echo '<td>';
                    echo '<input type="submit" value="Actualizar" name="update" onclick="return confirm(\'¿Estás seguro de actualizar este cliente?\');">';
                    echo ' <input type="submit" value="Eliminar" name="delete" onclick="return confirm(\'¿Estás seguro de eliminar este cliente?\');">';
                    echo '</td>';
                    echo '</form>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == "existe") {
                echo '<p><b>Error: Este carnet ya está registrado.</b></p>';
            }
        } else if (isset($_GET['success'])) {
            if ($_GET['success'] == "inserted") {
                echo '<p><b>Éxito: Cliente insertado correctamente.</b></p>';
            } else if ($_GET['success'] == "updated") {
                echo '<p><b>Éxito: Cliente actualizado correctamente.</b></p>';
            } else if ($_GET['success'] == "deleted") {
                echo '<p><b>Éxito: Cliente eliminado correctamente.</b></p>';
            }
        }
        ?>

    </main>

    <hr>

    <footer>
        <p>Fin de la página.</p>
    </footer>

</body>
</html>
