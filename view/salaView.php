<?php
include '../business/salaBusiness.php';
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
$salaBusiness = new SalaBusiness();

// Obtener todas las salas para mostrar
$salas = $salaBusiness->obtenerTbsala();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Salas</title>

    <script>
        function validarFormulario() {
            const nombre = document.forms["salaForm"]["nombre"].value;
            const capacidad = document.forms["salaForm"]["capacidad"].value;
            const regexNombre = /^[a-zA-Z0-9\s\u00C0-\u017F]+$/;
            const regexCapacidad = /^\d+$/;

            if (!regexNombre.test(nombre)) {
                alert("El nombre de la sala solo puede contener letras, números, espacios y tildes.");
                return false;
            }
            if (!regexCapacidad.test(capacidad) || parseInt(capacidad) <= 0) {
                alert("La capacidad debe ser un número entero positivo.");
                return false;
            }
            return confirm('¿Estás seguro de que deseas realizar esta acción?');
        }
    </script>
</head>
<body>

<header>
    <h2>Gym - Salas</h2>
    <a href="../index.php">Volver al Inicio</a>
</header>

<hr>

<main>
    <?php if ($tipoUsuario == 'admin') { ?>
        <!-- Vista de administrador - Puede ver, registrar, actualizar y eliminar salas -->
        <h2>Registrar Sala</h2>

        <form name="salaForm" method="post" action="../action/salaAction.php"
              onsubmit="return validarFormulario();">
            <label>Nombre de la Sala:</label><br/>
            <input type="text" name="nombre" required/><br/>

            <label>Capacidad:</label><br/>
            <input type="number" name="capacidad" min="1" required/><br/><br/>

            <input type="submit" value="Registrar Sala" name="insertar"/>
        </form>

        <br/><br/>

        <h2>Salas Registradas</h2>

        <table border="1">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($salas as $sala) {
                echo '<tr>';
                echo '<form method="post" action="../action/salaAction.php">';
                echo '<input type="hidden" name="id" value="' . $sala->getTbsalaid() . '">';
                echo '<td><input type="text" name="nombre" value="' . htmlspecialchars($sala->getTbsalanombre()) . '" required></td>';
                echo '<td><input type="number" name="capacidad" value="' . $sala->getTbsalacapacidad() . '" min="1" required></td>';

                echo '<td>';
                echo '<select name="estado" required>';
                echo '<option value="1" ' . ($sala->getTbsalaestado() == 1 ? 'selected' : '') . '>Activa</option>';
                echo '<option value="0" ' . ($sala->getTbsalaestado() == 0 ? 'selected' : '') . '>Inactiva</option>';
                echo '</select>';
                echo '</td>';

                echo '<td>';
                echo '<input type="submit" value="Actualizar" name="actualizar" onclick="return confirm(\'¿Estás seguro de actualizar esta sala?\');">';
                echo '<input type="submit" value="Eliminar" name="eliminar" onclick="return confirm(\'¿Estás seguro de eliminar esta sala?\');">';
                echo '</td>';

                echo '</form>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>

    <?php } else if ($tipoUsuario == 'instructor') { ?>
        <!-- Vista de instructor - Puede agregar, ver y actualizar salas -->
        <h2>Registrar Sala</h2>

        <form name="salaForm" method="post" action="../action/salaAction.php"
              onsubmit="return validarFormulario();">
            <label>Nombre de la Sala:</label><br/>
            <input type="text" name="nombre" required/><br/>

            <label>Capacidad:</label><br/>
            <input type="number" name="capacidad" min="1" required/><br/><br/>

            <input type="submit" value="Registrar Sala" name="insertar"/>
        </form>

        <br/><br/>

        <h2>Salas Registradas</h2>

        <table border="1">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($salas as $sala) {
                echo '<tr>';
                echo '<form method="post" action="../action/salaAction.php">';
                echo '<input type="hidden" name="id" value="' . $sala->getTbsalaid() . '">';
                echo '<td><input type="text" name="nombre" value="' . htmlspecialchars($sala->getTbsalanombre()) . '" required></td>';
                echo '<td><input type="number" name="capacidad" value="' . $sala->getTbsalacapacidad() . '" min="1" required></td>';

                echo '<td>';
                echo '<select name="estado" required>';
                echo '<option value="1" ' . ($sala->getTbsalaestado() == 1 ? 'selected' : '') . '>Activa</option>';
                echo '<option value="0" ' . ($sala->getTbsalaestado() == 0 ? 'selected' : '') . '>Inactiva</option>';
                echo '</select>';
                echo '</td>';

                echo '<td>';
                echo '<input type="submit" value="Actualizar" name="actualizar" onclick="return confirm(\'¿Estás seguro de actualizar esta sala?\');">';
                echo '</td>';

                echo '</form>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>

    <?php } else { ?>
        <!-- Vista de cliente - Solo puede ver la lista de salas -->
        <h2>Salas Disponibles</h2>

        <table border="1">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Estado</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($salas as $sala) {
                echo '<tr>';

                echo '<td>' . htmlspecialchars($sala->getTbsalanombre()) . '</td>';
                echo '<td>' . $sala->getTbsalacapacidad() . '</td>';
                echo '<td>' . ($sala->getTbsalaestado() == 1 ? 'Activa' : 'Inactiva') . '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    <?php } ?>

    <?php
    if (isset($_GET['error']) && !empty($_GET['error'])) {
        $error = $_GET['error'];
        if ($error == "datos_faltantes") {
            echo '<p><b>Error: Datos incompletos.</b></p>';
        } else if ($error == "insertar") {
            echo '<p><b>Error: No se pudo insertar la sala.</b></p>';
        } else if ($error == "actualizar") {
            echo '<p><b>Error: No se pudo actualizar la sala.</b></p>';
        } else if ($error == "eliminar") {
            echo '<p><b>Error: No se pudo eliminar la sala.</b></p>';
        } else if ($error == "id_faltante") {
            echo '<p><b>Error: ID faltante para eliminar.</b></p>';
        } else if ($error == "accion_no_valida") {
            echo '<p><b>Error: Acción no válida.</b></p>';
        }
    } else if (isset($_GET['success']) && !empty($_GET['success'])) {
        $success = $_GET['success'];
        if ($success == "insertado") {
            echo '<p><b>Éxito: Sala insertada correctamente.</b></p>';
        } else if ($success == "actualizado") {
            echo '<p><b>Éxito: Sala actualizada correctamente.</b></p>';
        } else if ($success == "eliminado") {
            echo '<p><b>Éxito: Sala eliminada correctamente.</b></p>';
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