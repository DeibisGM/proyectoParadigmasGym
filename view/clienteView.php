<?php
include '../business/clienteBusiness.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    <h2>Gym - Clientes</h2>
    <a href="../index.php">Volver al Inicio</a>
</header>

<hr>

<main>
    <h2>Registrar Cliente</h2>

    <form name="clienteForm" method="post" action="../action/clienteAction.php" onsubmit="return validarFormulario();">
        <label>Carnet:</label><br />
        <input type="text" name="carnet" required /><br />

        <label>Nombre:</label><br />
        <input type="text" name="nombre" required /><br />

        <label>Fecha de nacimiento:</label><br />
        <input type="date" name="fechaNacimiento" required /><br />

        <label>Teléfono:</label><br />
        <input type="text" name="telefono" maxlength="8" required /><br />

        <label>Correo (opcional):</label><br />
        <input type="email" name="correo" /><br />

        <label>Dirección:</label><br />
        <input type="text" name="direccion" required /><br />

        <label>Género:</label><br />
        <select name="genero" required>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
            <option value="Otro">Otro</option>
        </select><br />

        <label>Fecha de inscripción:</label><br />
        <input type="date" name="fechaInscripcion" required /><br /><br />

        <input type="submit" value="Registrar Cliente" name="insertar" />
    </form>

    <br /><br />

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
                echo '<input type="hidden" name="carnet" value="' . htmlspecialchars($cliente->getCarnet()) . '">';
                echo '<td><input type="text" name="nombre" value="' . htmlspecialchars($cliente->getNombre()) . '" required></td>';
                echo '<td>' . htmlspecialchars($cliente->getCarnet()) . '</td>';
                echo '<td><input type="date" name="fechaNacimiento" value="' . $cliente->getFechaNacimiento() . '" required></td>';
                echo '<td><input type="text" name="telefono" value="' . htmlspecialchars($cliente->getTelefono()) . '" maxlength="8" required></td>';
                echo '<td><input type="email" name="correo" value="' . htmlspecialchars($cliente->getCorreo()) . '"></td>';
                echo '<td><input type="text" name="direccion" value="' . htmlspecialchars($cliente->getDireccion()) . '" required></td>';
                echo '<td><select name="genero" required>';
                echo '<option value="M" ' . ($cliente->getGenero() == 'M' ? 'selected' : '') . '>Masculino</option>';
                echo '<option value="F" ' . ($cliente->getGenero() == 'F' ? 'selected' : '') . '>Femenino</option>';
                echo '<option value="Otro" ' . ($cliente->getGenero() == 'Otro' ? 'selected' : '') . '>Otro</option>';
                echo '</select></td>';
                echo '<td><input type="date" name="fechaInscripcion" value="' . $cliente->getInscripcion() . '" required></td>';

                echo '<td>';
                echo '<select name="estado" required>';
                echo '<option value="1" ' . ($cliente->getEstado() == 1 ? 'selected' : '') . '>Activo</option>';
                echo '<option value="0" ' . ($cliente->getEstado() == 0 ? 'selected' : '') . '>Inactivo</option>';
                echo '</select>';
                echo '</td>';

                echo '<td>';
                echo '<input type="submit" value="Actualizar" name="actualizar" onclick="return confirm(\'¿Estás seguro de actualizar este cliente?\');">';
                echo '<input type="submit" value="Eliminar" name="eliminar" onclick="return confirm(\'¿Estás seguro de eliminar este cliente?\');">';
                echo '</td>';

                echo '</form>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <?php
    if (isset($_GET['error']) && !empty($_GET['error'])) {
        $error = $_GET['error'];
        if ($error == "existe") {
            echo '<p><b>Error: Este carnet ya está registrado.</b></p>';
        } else if ($error == "datos_faltantes") {
            echo '<p><b>Error: Datos incompletos.</b></p>';
        } else if ($error == "insertar") {
            echo '<p><b>Error: No se pudo insertar el cliente.</b></p>';
        } else if ($error == "actualizar") {
            echo '<p><b>Error: No se pudo actualizar el cliente.</b></p>';
        } else if ($error == "eliminar") {
            echo '<p><b>Error: No se pudo eliminar el cliente.</b></p>';
        } else if ($error == "id_faltante") {
            echo '<p><b>Error: ID faltante para eliminar.</b></p>';
        } else if ($error == "accion_no_valida") {
            echo '<p><b>Error: Acción no válida.</b></p>';
        }
    } else if (isset($_GET['success']) && !empty($_GET['success'])) {
        $success = $_GET['success'];
        if ($success == "insertado") {
            echo '<p><b>Éxito: Cliente insertado correctamente.</b></p>';
        } else if ($success == "actualizado") {
            echo '<p><b>Éxito: Cliente actualizado correctamente.</b></p>';
        } else if ($success == "eliminado") {
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
