<?php
session_start();
include '../business/clienteBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../utility/Validation.php';
Validation::start();

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
    <script src="../utility/Events.js"></script>

</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-users"></i>Clientes</h2>
    </header>

    <main>
        <?php
        // Mensajes de error o éxito
        $generalError = Validation::getError('general');
        if ($generalError) {
            echo '<p class="error-message flash-msg"><b>Error: ' . htmlspecialchars($generalError) . '</b></p>';
        } else if (isset($_GET['error'])) {
            $error = $_GET['error'];
            echo '<p class="error-message flash-msg"><b>Error: ';
            if ($error == "existe") echo 'Este carnet ya está registrado.';
            else if ($error == "datos_faltantes") echo 'Datos incompletos.';
            else if ($error == "insertar") echo 'No se pudo insertar el cliente.';
            else if ($error == "actualizar") echo 'No se pudo actualizar el cliente.';
            else if ($error == "eliminar") echo 'No se pudo eliminar el cliente.';
            else if ($error == "dbError") echo 'Error en la base de datos.';
            else echo 'Acción no válida.';
            echo '</b></p>';
        } else if (isset($_GET['success'])) {
            $success = $_GET['success'];
            echo '<p class="success-message flash-msg"><b>Éxito: ';
            if ($success == "inserted") echo 'Cliente insertado correctamente.';
            else if ($success == "updated") echo 'Cliente actualizado correctamente.';
            else if ($success == "eliminado") echo 'Cliente eliminado correctamente.';
            else if ($success == "image_deleted") echo 'Imagen eliminada correctamente.';
            echo '</b></p>';
        }
        ?>

        <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') { ?>
            <section>
                <h3><i class="ph ph-user-plus"></i>Registrar Cliente</h3>
                <form name="clienteForm" method="post" action="../action/clienteAction.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Identificación: </label>
                        <span class="error-message"><?= Validation::getError('carnet') ?></span>
                        <input type="text" name="carnet" maxlength="10" placeholder="Carnet" value="<?= Validation::getOldInput('carnet') ?>">
                    </div>
                    <div class="form-group">
                        <label>Nombre: </label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" maxlength="50" placeholder="Nombre" value="<?= Validation::getOldInput('nombre') ?>">
                    </div>
                    <div class="form-group">
                        <label>Fecha de nacimiento: </label>
                        <span class="error-message"><?= Validation::getError('fechaNacimiento') ?></span>
                        <input type="date" name="fechaNacimiento" value="<?= Validation::getOldInput('fechaNacimiento') ?>">
                    </div>
                    <div class="form-group">
                        <label>Telefono: </label>
                        <span class="error-message"><?= Validation::getError('telefono') ?></span>
                        <input type="text" name="telefono" maxlength="8" placeholder="Teléfono" value="<?= Validation::getOldInput('telefono') ?>">
                    </div>
                    <div class="form-group">
                        <label>Correo: </label>
                        <span class="error-message"><?= Validation::getError('correo') ?></span>
                        <input type="email" name="correo" maxlength="100" placeholder="Correo" value="<?= Validation::getOldInput('correo') ?>">
                    </div>
                    <div class="form-group">
                        <label>Contraseña: </label>
                        <span class="error-message"><?= Validation::getError('contrasena') ?></span>
                        <input type="password" name="contrasena" maxlength="8" placeholder="Contraseña" value="<?= Validation::getOldInput('contrasena') ?>">
                    </div>
                    <div class="form-group">
                        <label>Direccion: </label>
                        <span class="error-message"><?= Validation::getError('direccion') ?></span>
                        <input type="text" name="direccion" maxlength="100" placeholder="Dirección" value="<?= Validation::getOldInput('direccion') ?>">
                    </div>
                    <div class="form-group">
                        <label>Genero: </label>
                        <span class="error-message"><?= Validation::getError('genero') ?></span>
                        <select name="genero">
                            <option value="M" <?= (Validation::getOldInput('genero')=='M'?'selected':'') ?>>Masculino</option>
                            <option value="F" <?= (Validation::getOldInput('genero')=='F'?'selected':'') ?>>Femenino</option>
                            <option value="Otro" <?= (Validation::getOldInput('genero')=='Otro'?'selected':'') ?>>Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha de inscripción: </label>
                        <span class="error-message"><?= Validation::getError('fechaInscripcion') ?></span>
                        <input type="date" name="fechaInscripcion" value="<?= Validation::getOldInput('fechaInscripcion') ?>">
                    </div>
                    <div class="form-group">
                        <label>Foto de cliente:</label>
                        <input type="file" name="tbclienteimagenid[]" accept="image/png, image/jpeg, image/webp">
                    </div>
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

                        foreach ($clienteBusiness->getAllTBCliente() as $c): 
                            $rowId = $c->getId();
                            ?>
                            <tr>
                                <form id="form-<?= $rowId ?>" method="post" action="../action/clienteAction.php" enctype="multipart/form-data"></form>
                                <input type="hidden" name="id" value="<?= $rowId ?>" form="form-<?= $rowId ?>">
                                <input type="hidden" name="carnet" value="<?= htmlspecialchars($c->getCarnet()) ?>" form="form-<?= $rowId ?>">
                                <td>
                                    <span class="error-message"><?= Validation::getError('nombre_'.$rowId) ?></span>
                                    <input type="text" name="nombre" maxlength="50" value="<?= htmlspecialchars(Validation::getOldInput('nombre_'.$rowId, $c->getNombre())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td><?= htmlspecialchars($c->getCarnet()) ?></td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('fechaNacimiento_'.$rowId) ?></span>
                                    <input type="date" name="fechaNacimiento" value="<?= Validation::getOldInput('fechaNacimiento_'.$rowId, $c->getFechaNacimiento()) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('telefono_'.$rowId) ?></span>
                                    <input type="text" name="telefono" maxlength="8" value="<?= htmlspecialchars(Validation::getOldInput('telefono_'.$rowId, $c->getTelefono())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('correo_'.$rowId) ?></span>
                                    <input type="email" name="correo" maxlength="100" value="<?= htmlspecialchars(Validation::getOldInput('correo_'.$rowId, $c->getCorreo())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('contrasena_'.$rowId) ?></span>
                                    <input type="password" name="contrasena" maxlength="8" value="<?= htmlspecialchars(Validation::getOldInput('contrasena_'.$rowId, $c->getContrasena())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('direccion_'.$rowId) ?></span>
                                    <input type="text" name="direccion" maxlength="100" value="<?= htmlspecialchars(Validation::getOldInput('direccion_'.$rowId, $c->getDireccion())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('genero_'.$rowId) ?></span>
                                    <select name="genero" form="form-<?= $rowId ?>">
                                        <option value="M" <?= Validation::getOldInput('genero_'.$rowId, $c->getGenero())=='M'?'selected':'' ?>>Masculino</option>
                                        <option value="F" <?= Validation::getOldInput('genero_'.$rowId, $c->getGenero())=='F'?'selected':'' ?>>Femenino</option>
                                        <option value="Otro" <?= Validation::getOldInput('genero_'.$rowId, $c->getGenero())=='Otro'?'selected':'' ?>>Otro</option>
                                    </select>
                                </td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('fechaInscripcion_'.$rowId) ?></span>
                                    <input type="date" name="fechaInscripcion" value="<?= Validation::getOldInput('fechaInscripcion_'.$rowId, $c->getInscripcion()) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td>
                                    <span class="error-message"><?= Validation::getError('estado_'.$rowId) ?></span>
                                    <select name="estado" form="form-<?= $rowId ?>">
                                        <option value="1" <?= Validation::getOldInput('estado_'.$rowId, $c->getActivo())==1?'selected':'' ?>>Activo</option>
                                        <option value="0" <?= Validation::getOldInput('estado_'.$rowId, $c->getActivo())==0?'selected':'' ?>>Inactivo</option>
                                    </select>
                                </td>
                                <td>
                                    <?php
                                    $img = $imageManager->getImagesByIds($c->getTbclienteImagenId());
                                    if (!empty($img)) {
                                        $src = '..' . htmlspecialchars($img[0]['tbimagenruta']) . '?t=' . time();
                                        echo '<div class="image-container"><img src="'.$src.'" alt="Imagen"><button type="submit" name="delete_image" value="'.$c->getTbclienteImagenId().'" onclick="return confirm(\'¿Eliminar esta imagen?\');" form="form-'.$rowId.'">X</button></div>';
                                    } else {
                                        echo '<input type="file" name="tbclienteimagenid[]" form="form-'.$rowId.'">';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="submit" name="actualizar" form="form-<?= $rowId ?>"><i class="ph ph-pencil-simple"></i>Actualizar</button>
                                    <button type="submit" name="eliminar" onclick="return confirm('¿Estás seguro de eliminar este cliente?');" form="form-<?= $rowId ?>"><i class="ph ph-trash"></i>Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php } else { ?>
            <section>
                <h3><i class="ph ph-user-circle"></i>Mi Información</h3>
                <?php if ($cliente): ?>
                    <form method="post" action="../action/clienteAction.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $cliente->getId() ?>">
                        <input type="hidden" name="carnet" value="<?= htmlspecialchars($cliente->getCarnet()) ?>">

                        <div class="form-group">
                            <label>Carnet:</label>
                            <p><?= htmlspecialchars($cliente->getCarnet()) ?></p>
                        </div>
                        <div class="form-group">
                            <span class="error-message"><?= Validation::getError('nombre') ?></span>
                            <label>Nombre:</label>
                            <input type="text" name="nombre" maxlength="50" value="<?= Validation::getOldInput('nombre', htmlspecialchars($cliente->getNombre())) ?>">
                        </div>
                        <div class="form-group">
                            <span class="error-message"><?= Validation::getError('fechaNacimiento') ?></span>
                            <label>Fecha de nacimiento:</label>
                            <input type="date" name="fechaNacimiento" value="<?= Validation::getOldInput('fechaNacimiento', $cliente->getFechaNacimiento()) ?>">
                        </div>
                        <div class="form-group">
                            <span class="error-message"><?= Validation::getError('telefono') ?></span>
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" maxlength="8" value="<?= Validation::getOldInput('telefono', htmlspecialchars($cliente->getTelefono())) ?>">
                        </div>
                        <div class="form-group">
                            <span class="error-message"><?= Validation::getError('correo') ?></span>
                            <label>Correo:</label>
                            <input type="email" name="correo" maxlength="100" value="<?= Validation::getOldInput('correo', htmlspecialchars($cliente->getCorreo())) ?>">
                        </div>
                        <div class="form-group">
                            <span class="error-message"><?= Validation::getError('contrasena') ?></span>
                            <label>Contraseña:</label>
                            <input type="password" name="contrasena" maxlength="8" value="<?= Validation::getOldInput('contrasena', htmlspecialchars($cliente->getContrasena())) ?>">
                        </div>
                        <div class="form-group">
                            <span class="error-message"><?= Validation::getError('direccion') ?></span>
                            <label>Dirección:</label>
                            <input type="text" name="direccion" maxlength="100" value="<?= Validation::getOldInput('direccion', htmlspecialchars($cliente->getDireccion())) ?>">
                        </div>
                        <div class="form-group">
                            <span class="error-message"><?= Validation::getError('genero') ?></span>
                            <label>Género:</label>
                            <select name="genero">
                                <option value="M" <?= (Validation::getOldInput('genero', $cliente->getGenero())=='M'?'selected':'') ?>>Masculino</option>
                                <option value="F" <?= (Validation::getOldInput('genero', $cliente->getGenero())=='F'?'selected':'') ?>>Femenino</option>
                                <option value="Otro" <?= (Validation::getOldInput('genero', $cliente->getGenero())=='Otro'?'selected':'') ?>>Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha de inscripción:</label>
                            <input type="date" name="fechaInscripcion" value="<?= Validation::getOldInput('fechaInscripcion', $cliente->getInscripcion()) ?>">
                        </div>
                        <input type="hidden" name="estado" value="<?= $cliente->getActivo() ?>">

                        <div class="form-group">
                            <label>Foto de perfil:</label>
                            <?php
                            $img = $imageManager->getImagesByIds($cliente->getTbclienteImagenId());
                            if (!empty($img)) {
                                $src = '..' . htmlspecialchars($img[0]['tbimagenruta']) . '?t=' . time();
                                echo '<div class="image-container"><img src="'.$src.'" alt="Imagen"><button type="submit" name="delete_image" value="'.$cliente->getTbclienteImagenId().'" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button></div>';
                            } else {
                                echo '<input type="file" name="tbclienteimagenid[]">';
                            }
                            ?>
                        </div>
                        <button type="submit" name="actualizar"><i class="ph ph-floppy-disk"></i>Actualizar Mis Datos</button>
                    </form>
                <?php else: ?>
                    <p>No se pudo encontrar tu información. Por favor, contacta al administrador.</p>
                <?php endif; ?>
            </section>
        <?php } ?>
    </main>

    <footer>
        <p>&copy; <?= date("Y") ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
<?php Validation::clear(); ?>
</body>
</html>