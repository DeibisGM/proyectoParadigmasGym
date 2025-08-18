<?php
include '../business/clienteBusiness.php';
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    // Si no hay sesión, redirigir al login
    header("Location: loginView.php");
    exit();
}

// Obtener información del usuario
$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
$nombreUsuario = $_SESSION['usuario_nombre'];

// Inicializar el objeto de negocio
$clienteBusiness = new ClienteBusiness();

// Si es cliente, obtener solo sus datos
if ($tipoUsuario == 'cliente') {
    $cliente = $clienteBusiness->getClientePorId($usuarioId);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Clientes</title>

    <script>
        function validarFormulario() {
            const nombre = document.forms["clienteForm"]["nombre"].value;
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
            return confirm('¿Estás seguro de que deseas realizar esta acción?');
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
    <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') { ?>
        <!-- Vista de administrador / instructor - Puede ver y gestionar todos los clientes -->
        <h2>Registrar Cliente</h2>

        <form name="clienteForm" method="post" action="../action/clienteAction.php"
              onsubmit="return validarFormulario();">
            <label>Carnet:</label><br/>
            <input type="text" name="carnet" required/><br/>

            <label>Nombre:</label><br/>
            <input type="text" name="nombre" required/><br/>

            <label>Fecha de nacimiento:</label><br/>
            <input type="date" name="fechaNacimiento" required/><br/>

            <label>Teléfono:</label><br/>
            <input type="text" name="telefono" maxlength="8" required/><br/>

            <label>Correo:</label><br/>
            <input type="email" name="correo" required/><br/>

            <label>Contraseña:</label><br/>
            <input type="password" name="contrasena" required/><br/>

            <label>Dirección:</label><br/>
            <input type="text" name="direccion" required/><br/>

            <label>Género:</label><br/>
            <select name="genero" required>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="Otro">Otro</option>
            </select><br/>

            <label>Fecha de inscripción:</label><br/>
            <input type="date" name="fechaInscripcion" required/><br/><br/>

            <input type="submit" value="Registrar Cliente" name="insertar"/>
        </form>

        <br/><br/>

        <h2>Clientes Registrados</h2>

        <table border="1">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Carnet</th>
                <th>Fecha Nacimiento</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Dirección</th>
                <th>Género</th>
                <th>Inscripción</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
            </thead>
            <tbody>
            <?php
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
                echo '<td><input type="email" name="correo" value="' . htmlspecialchars($cliente->getCorreo()) . '" required></td>';
                echo '<td><input type="password" name="contrasena" value="' . htmlspecialchars($cliente->getContrasena()) . '" required></td>';
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
    <?php } else { ?>
        <!-- Vista de cliente - Solo puede ver y editar su propia información -->
        <h2>Mi Información</h2>

        <?php if ($cliente) { ?>
            <form method="post" action="../action/clienteAction.php" onsubmit="return validarFormulario();">
                <input type="hidden" name="id" value="<?php echo $cliente->getId(); ?>">
                <input type="hidden" name="carnet" value="<?php echo htmlspecialchars($cliente->getCarnet()); ?>">

                <label>Carnet:</label><br>
                <p><?php echo htmlspecialchars($cliente->getCarnet()); ?></p>

                <label>Nombre:</label><br>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($cliente->getNombre()); ?>"
                       required><br>

                <label>Fecha de nacimiento:</label><br>
                <input type="date" name="fechaNacimiento" value="<?php echo $cliente->getFechaNacimiento(); ?>"
                       required><br>

                <label>Teléfono:</label><br>
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($cliente->getTelefono()); ?>"
                       maxlength="8" required><br>

                <label>Correo:</label><br>
                <input type="email" name="correo" value="<?php echo htmlspecialchars($cliente->getCorreo()); ?>"
                       required><br>

                <label>Contraseña:</label><br>
                <input type="password" name="contrasena"
                       value="<?php echo htmlspecialchars($cliente->getContrasena()); ?>" required><br>

                <label>Dirección:</label><br>
                <input type="text" name="direccion" value="<?php echo htmlspecialchars($cliente->getDireccion()); ?>"
                       required><br>

                <label>Género:</label><br>
                <select name="genero" required>
                    <option value="M" <?php echo($cliente->getGenero() == 'M' ? 'selected' : ''); ?>>Masculino</option>
                    <option value="F" <?php echo($cliente->getGenero() == 'F' ? 'selected' : ''); ?>>Femenino</option>
                    <option value="Otro" <?php echo($cliente->getGenero() == 'Otro' ? 'selected' : ''); ?>>Otro</option>
                </select><br>

                <label>Fecha de inscripción:</label><br>
                <input type="date" name="fechaInscripcion" value="<?php echo $cliente->getInscripcion(); ?>"
                       required><br>

                <input type="hidden" name="estado" value="<?php echo $cliente->getEstado(); ?>">

                <br>
                <input type="submit" value="Actualizar Mis Datos" name="actualizar">
            </form>
        <?php } else { ?>
            <p>No se pudo encontrar tu información. Por favor, contacta al administrador.</p>
        <?php } ?>
    <?php } ?>

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
