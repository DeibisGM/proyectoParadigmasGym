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
    <title>Gestión de Salas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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

<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-door"></i>Gestión de Salas</h2>

    </header>

    <main>
        <?php
        if (isset($_GET['error']) && !empty($_GET['error'])) {
            $error = $_GET['error'];
            echo '<p class="error-message"><b>Error: ';
            if ($error == "datos_faltantes") echo 'Datos incompletos.';
            else if ($error == "insertar") echo 'No se pudo insertar la sala.';
            else if ($error == "actualizar") echo 'No se pudo actualizar la sala.';
            else if ($error == "eliminar") echo 'No se pudo eliminar la sala.';
            else echo 'Acción no válida.';
            echo '</b></p>';
        } else if (isset($_GET['success']) && !empty($_GET['success'])) {
            $success = $_GET['success'];
            echo '<p class="success-message"><b>Éxito: ';
            if ($success == "insertado") echo 'Sala insertada correctamente.';
            else if ($success == "actualizado") echo 'Sala actualizada correctamente.';
            else if ($success == "eliminado") echo 'Sala eliminada correctamente.';
            echo '</b></p>';
        }
        ?>

        <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') { ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Registrar Sala</h3>
                <form name="salaForm" method="post" action="../action/salaAction.php"
                      onsubmit="return validarFormulario();">
                    <label>Nombre de la Sala:</label>
                    <input type="text" name="nombre" placeholder="Ej: Sala de Yoga" required/>
                    <label>Capacidad:</label>
                    <input type="number" name="capacidad" min="1" placeholder="Ej: 15" required/>
                    <button type="submit" name="insertar"><i class="ph ph-plus"></i>Registrar Sala</button>
                </form>
            </section>
        <?php } ?>


        <section>
            <h3><i class="ph ph-list-bullets"></i>Salas Registradas</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') echo '<th>Acción</th>'; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($salas as $sala): ?>
                        <tr>
                            <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor'): ?>
                                <form method="post" action="../action/salaAction.php">
                                    <input type="hidden" name="id" value="<?php echo $sala->getTbsalaid(); ?>">
                                    <td><input type="text" name="nombre"
                                               value="<?php echo htmlspecialchars($sala->getTbsalanombre()); ?>"
                                               required></td>
                                    <td><input type="number" name="capacidad"
                                               value="<?php echo $sala->getTbsalacapacidad(); ?>" min="1" required>
                                    </td>
                                    <td>
                                        <select name="estado" required>
                                            <option value="1" <?php echo($sala->getTbsalaestado() == 1 ? 'selected' : ''); ?>>
                                                Activa
                                            </option>
                                            <option value="0" <?php echo($sala->getTbsalaestado() == 0 ? 'selected' : ''); ?>>
                                                Inactiva
                                            </option>
                                        </select>
                                    </td>
                                    <td class="actions-cell">
                                        <button type="submit" name="actualizar" title="Actualizar"
                                                onclick="return confirm('¿Estás seguro de actualizar esta sala?');"><i
                                                    class="ph ph-pencil-simple"></i> Actualizar
                                        </button>
                                        <?php if ($tipoUsuario == 'admin'): ?>
                                            <button type="submit" name="eliminar" title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta sala?');"><i
                                                        class="ph ph-trash"></i> Eliminar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </form>
                            <?php else: // Vista Cliente ?>
                                <td><?php echo htmlspecialchars($sala->getTbsalanombre()); ?></td>
                                <td><?php echo $sala->getTbsalacapacidad(); ?></td>
                                <td><?php echo($sala->getTbsalaestado() == 1 ? 'Activa' : 'Inactiva'); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>