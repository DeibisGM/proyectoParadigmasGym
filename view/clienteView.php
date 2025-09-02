<?php
include '../business/clienteBusiness.php';
include_once '../utility/ImageManager.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: loginView.php");
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
$nombreUsuario = $_SESSION['usuario_nombre'];

$clienteBusiness = new ClienteBusiness();
$imageManager = new ImageManager();

if ($tipoUsuario == 'cliente') {
    $cliente = $clienteBusiness->getClientePorId($usuarioId);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-users"></i>Clientes</h2>

    </header>

    <main>
        <?php
        if (isset($_GET['error']) && !empty($_GET['error'])) {
            $error = $_GET['error'];
            echo '<p class="error-message"><b>Error: ';
            if ($error == "existe") echo 'Este carnet ya está registrado.';
            else if ($error == "datos_faltantes") echo 'Datos incompletos.';
            else if ($error == "insertar") echo 'No se pudo insertar el cliente.';
            else if ($error == "actualizar") echo 'No se pudo actualizar el cliente.';
            else if ($error == "eliminar") echo 'No se pudo eliminar el cliente.';
            else echo 'Acción no válida.';
            echo '</b></p>';
        } else if (isset($_GET['success']) && !empty($_GET['success'])) {
            $success = $_GET['success'];
            echo '<p class="success-message"><b>Éxito: ';
            if ($success == "insertado") echo 'Cliente insertado correctamente.';
            else if ($success == "actualizado") echo 'Cliente actualizado correctamente.';
            else if ($success == "eliminado") echo 'Cliente eliminado correctamente.';
            echo '</b></p>';
        }
        ?>

        <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') { ?>
            <section>
                <h3><i class="ph ph-user-plus"></i>Registrar Cliente</h3>
                <form name="clienteForm" method="post" action="../action/clienteAction.php"
                      enctype="multipart/form-data">
                    <label>Carnet:</label>
                    <input type="text" name="carnet" placeholder="Carnet" required/>
                    <label>Nombre:</label>
                    <input type="text" name="nombre" placeholder="Nombre" required/>
                    <label>Fecha de nacimiento:</label>
                    <input type="date" name="fechaNacimiento" required/>
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" placeholder="Teléfono" maxlength="8" required/>
                    <label>Correo:</label>
                    <input type="email" name="correo" placeholder="Correo" required/>
                    <label>Contraseña:</label>
                    <input type="password" name="contrasena" placeholder="Contraseña" required/>
                    <label>Dirección:</label>
                    <input type="text" name="direccion" placeholder="Dirección" required/>
                    <label>Género:</label>
                    <select name="genero" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <label>Fecha de inscripción:</label>
                    <input type="date" name="fechaInscripcion" required/>
                    <label>Foto de cliente:</label><br><br>
                    <input type="file" name="tbclienteimagenid[]" accept="image/png, image/jpeg, image/webp">
                    <br><br>
                    <button type="submit" name="insertar"><i class="ph ph-plus"></i>Registrar Cliente</button>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-list-bullets"></i>Clientes Registrados</h3>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Carnet</th>
                            <th>Nacimiento</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Contraseña</th>
                            <th>Dirección</th>
                            <th>Género</th>
                            <th>Inscripción</th>
                            <th>Estado</th>
                            <th>Imagen</th>
                            <th>Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $clientes = $clienteBusiness->getAllTBCliente();
                        foreach ($clientes as $cliente) {
                            echo '<tr>';
                            echo '<form method="post" action="../action/clienteAction.php" enctype="multipart/form-data">';
                            echo '<input type="hidden" name="id" value="' . $cliente->getId() . '">';
                            echo '<input type="hidden" name="carnet" value="' . htmlspecialchars($cliente->getCarnet()) . '">';
                            echo '<td><input type="text" name="nombre" value="' . htmlspecialchars($cliente->getNombre()) . '" placeholder="Nombre" required></td>';
                            echo '<td>' . htmlspecialchars($cliente->getCarnet()) . '</td>';
                            echo '<td><input type="date" name="fechaNacimiento" value="' . $cliente->getFechaNacimiento() . '" placeholder="Fecha de Nacimiento" required></td>';
                            echo '<td><input type="text" name="telefono" value="' . htmlspecialchars($cliente->getTelefono()) . '" placeholder="Teléfono" maxlength="8" required></td>';
                            echo '<td><input type="email" name="correo" value="' . htmlspecialchars($cliente->getCorreo()) . '" placeholder="Correo" required></td>';
                            echo '<td><input type="password" name="contrasena" value="' . htmlspecialchars($cliente->getContrasena()) . '" placeholder="Contraseña" required></td>';
                            echo '<td><input type="text" name="direccion" value="' . htmlspecialchars($cliente->getDireccion()) . '" placeholder="Dirección" required></td>';
                            echo '<td><select name="genero" required>';
                            echo '<option value="M" ' . ($cliente->getGenero() == 'M' ? 'selected' : '') . '>Masculino</option>';
                            echo '<option value="F" ' . ($cliente->getGenero() == 'F' ? 'selected' : '') . '>Femenino</option>';
                            echo '<option value="Otro" ' . ($cliente->getGenero() == 'Otro' ? 'selected' : '') . '>Otro</option>';
                            echo '</select></td>';
                            echo '<td><input type="date" name="fechaInscripcion" value="' . $cliente->getInscripcion() . '" required></td>';
                            echo '<td><select name="estado" required><option value="1" ' . ($cliente->getEstado() == 1 ? 'selected' : '') . '>Activo</option><option value="0" ' . ($cliente->getEstado() == 0 ? 'selected' : '') . '>Inactivo</option></select></td>';
                            echo '<td>';
                            $imageId = $cliente->getTbclienteImagenId();
                            $imagen = $imageManager->getImagesByIds($imageId);
                            if (!empty($imagen)) {
                                $imagePath = '..' . htmlspecialchars($imagen[0]['tbimagenruta']);
                                $finalSrc = $imagePath . '?t=' . time();
                                echo '<div class="image-container"><img src="' . $finalSrc . '" alt="Imagen"><button type="submit" name="delete_image" value="' . $imageId . '" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button></div>';
                            } else {
                                echo '<input type="file" name="tbclienteimagenid[]">';
                            }
                            echo '</td>';
                            echo '<td><button type="submit" name="actualizar" onclick="return confirm(\'¿Estás seguro de actualizar este cliente?\');"><i class="ph ph-pencil-simple"></i> Actualizar</button><button type="submit" name="eliminar" onclick="return confirm(\'¿Estás seguro de eliminar este cliente?\');"><i class="ph ph-trash"></i> Eliminar</button></td>';
                            echo '</form></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php } else { ?>
            <section>
                <h3><i class="ph ph-user-circle"></i>Mi Información</h3>
                <?php if ($cliente) { ?>
                    <form method="post" action="../action/clienteAction.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $cliente->getId(); ?>">
                        <input type="hidden" name="carnet"
                               value="<?php echo htmlspecialchars($cliente->getCarnet()); ?>">
                        <label>Carnet:</label>
                        <p><?php echo htmlspecialchars($cliente->getCarnet()); ?></p>
                        <label>Nombre:</label><input type="text" name="nombre"
                                                     value="<?php echo htmlspecialchars($cliente->getNombre()); ?>"
                                                     placeholder="Nombre" required>
                        <label>Fecha de nacimiento:</label><input type="date" name="fechaNacimiento"
                                                                  value="<?php echo $cliente->getFechaNacimiento(); ?>"
                                                                  placeholder="Fecha de Nacimiento" required>
                        <label>Teléfono:</label><input type="text" name="telefono"
                                                       value="<?php echo htmlspecialchars($cliente->getTelefono()); ?>"
                                                       placeholder="Teléfono" maxlength="8" required>
                        <label>Correo:</label><input type="email" name="correo"
                                                     value="<?php echo htmlspecialchars($cliente->getCorreo()); ?>"
                                                     placeholder="Correo" required>
                        <label>Contraseña:</label><input type="password" name="contrasena"
                                                         value="<?php echo htmlspecialchars($cliente->getContrasena()); ?>"
                                                         placeholder="Contraseña" required>
                        <label>Dirección:</label><input type="text" name="direccion"
                                                        value="<?php echo htmlspecialchars($cliente->getDireccion()); ?>"
                                                        placeholder="Dirección" required>
                        <label>Género:</label>
                        <select name="genero" required>
                            <option value="M" <?php echo($cliente->getGenero() == 'M' ? 'selected' : ''); ?>>Masculino
                            </option>
                            <option value="F" <?php echo($cliente->getGenero() == 'F' ? 'selected' : ''); ?>>Femenino
                            </option>
                            <option value="Otro" <?php echo($cliente->getGenero() == 'Otro' ? 'selected' : ''); ?>>
                                Otro
                            </option>
                        </select>
                        <label>Fecha de inscripción:</label><input type="date" name="fechaInscripcion"
                                                                   value="<?php echo $cliente->getInscripcion(); ?>"
                                                                   required>
                        <input type="hidden" name="estado" value="<?php echo $cliente->getEstado(); ?>">
                        <label>Foto de perfil:</label><br><br>
                        <?php
                        $imageId = $cliente->getTbclienteImagenId();
                        $imagen = $imageManager->getImagesByIds($imageId);
                        if (!empty($imagen)) {
                            $imagePath = '..' . htmlspecialchars($imagen[0]['tbimagenruta']);
                            $finalSrc = $imagePath . '?t=' . time();
                            echo '<div class="image-container"><img src="' . $finalSrc . '" alt="Imagen"><button type="submit" name="delete_image" value="' . $imageId . '" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button></div>';
                        } else {
                            echo '<input type="file" name="tbclienteimagenid[]">';
                        }
                        ?>
                        <br><br>
                        <button type="submit" name="actualizar"><i class="ph ph-floppy-disk"></i>Actualizar Mis Datos
                        </button>
                    </form>
                <?php } else { ?>
                    <p>No se pudo encontrar tu información. Por favor, contacta al administrador.</p>
                <?php } ?>
            </section>
        <?php } ?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>