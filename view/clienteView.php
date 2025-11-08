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
    <style>
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.2rem;
        }
        .form-grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
    </style>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="../utility/Events.js"></script>

</head>
<body>
<div class="container">
<header>
        <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2>Gestión de Clientes</h2>
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
                    <div class="form-grid-container">
                        <div class="form-group">
                            <label for="carnet">Identificación: </label>
                            <?php $error = Validation::getError('carnet'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                            <input type="text" id="carnet" name="carnet" maxlength="10" placeholder="Ej: 123456789" value="<?= Validation::getOldInput('carnet') ?>">
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre: </label>
                            <?php $error = Validation::getError('nombre'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?><input type="text" id="nombre" name="nombre" maxlength="50" placeholder="Ej: Juan Pérez" value="<?= Validation::getOldInput('nombre') ?>">
                        </div>
                        <div class="form-group">
                            <label for="fechaNacimiento">Fecha de Nacimiento: </label>
                            <?php $error = Validation::getError('fechaNacimiento'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                            <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?= Validation::getOldInput('fechaNacimiento') ?>">
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono: </label>
                            <?php $error = Validation::getError('telefono'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                            <input type="text" id="telefono" name="telefono" maxlength="8" placeholder="Ej: 88888888" value="<?= Validation::getOldInput('telefono') ?>">
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo: </label>
                            <?php $error = Validation::getError('correo'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?><input type="email" id="correo" name="correo" maxlength="100" placeholder="ejemplo@correo.com" value="<?= Validation::getOldInput('correo') ?>">
                        </div>
                        <div class="form-group">
                            <label for="contrasena">Contraseña: </label>
                            <?php $error = Validation::getError('contrasena'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                            <div class="password-group">
                                <input type="password" id="contrasena" name="contrasena" maxlength="8" placeholder="********" value="<?= Validation::getOldInput('contrasena') ?>">
                                <i class="ph ph-eye" id="togglePassword"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección: </label>
                            <?php $error = Validation::getError('direccion'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                            <input type="text" id="direccion" name="direccion" maxlength="100" placeholder="Ej: San José, Costa Rica" value="<?= Validation::getOldInput('direccion') ?>">
                        </div>
                        <div class="form-group">
                            <label for="genero">Género: </label>
                            <?php $error = Validation::getError('genero'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                            <select id="genero" name="genero">
                                <option value="M" <?= (Validation::getOldInput('genero')=='M'?'selected':'') ?>>Masculino</option>
                                <option value="F" <?= (Validation::getOldInput('genero')=='F'?'selected':'') ?>>Femenino</option>
                                <option value="Otro" <?= (Validation::getOldInput('genero')=='Otro'?'selected':'') ?>>Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fechaInscripcion">Fecha de inscripción: </label>
                            <?php $error = Validation::getError('fechaInscripcion'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                            <input type="date" id="fechaInscripcion" name="fechaInscripcion" value="<?= Validation::getOldInput('fechaInscripcion') ?>">
                        </div>
                        <div class="form-group form-group-horizontal">
                            <label for="tbclienteimagenid">Foto de cliente:</label>
                            <input type="file" id="tbclienteimagenid" name="tbclienteimagenid[]" accept="image/png, image/jpeg, image/webp">
                        </div>
                    </div>
                    <button type="submit" name="insertar"><i class="ph ph-plus"></i>Registrar Cliente</button>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-list-bullets"></i>Clientes Registrados</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
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
                                <td data-label="Nombre">
                                    <?php $error = Validation::getError('nombre_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <input type="text" name="nombre" maxlength="50" value="<?= htmlspecialchars(Validation::getOldInput('nombre_'.$rowId, $c->getNombre())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td data-label="Carnet"><?= htmlspecialchars($c->getCarnet()) ?></td>
                                <td data-label="Nacimiento">
                                    <?php $error = Validation::getError('fechaNacimiento_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <input type="date" name="fechaNacimiento" value="<?= Validation::getOldInput('fechaNacimiento_'.$rowId, $c->getFechaNacimiento()) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td data-label="Teléfono">
                                    <?php $error = Validation::getError('telefono_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <input type="text" name="telefono" maxlength="8" value="<?= htmlspecialchars(Validation::getOldInput('telefono_'.$rowId, $c->getTelefono())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td data-label="Correo">
                                    <?php $error = Validation::getError('correo_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <input type="email" name="correo" maxlength="100" value="<?= htmlspecialchars(Validation::getOldInput('correo_'.$rowId, $c->getCorreo())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td data-label="Contraseña">
                                    <?php $error = Validation::getError('contrasena_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <input type="password" name="contrasena" maxlength="8" value="<?= htmlspecialchars(Validation::getOldInput('contrasena_'.$rowId, $c->getContrasena())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td data-label="Dirección">
                                    <?php $error = Validation::getError('direccion_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <input type="text" name="direccion" maxlength="100" value="<?= htmlspecialchars(Validation::getOldInput('direccion_'.$rowId, $c->getDireccion())) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td data-label="Género">
                                    <?php $error = Validation::getError('genero_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <select name="genero" form="form-<?= $rowId ?>">
                                        <option value="M" <?= Validation::getOldInput('genero_'.$rowId, $c->getGenero())=='M'?'selected':'' ?>>Masculino</option>
                                        <option value="F" <?= Validation::getOldInput('genero_'.$rowId, $c->getGenero())=='F'?'selected':'' ?>>Femenino</option>
                                        <option value="Otro" <?= Validation::getOldInput('genero_'.$rowId, $c->getGenero())=='Otro'?'selected':'' ?>>Otro</option>
                                    </select>
                                </td>
                                <td data-label="Inscripción">
                                    <?php $error = Validation::getError('fechaInscripcion_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <input type="date" name="fechaInscripcion" value="<?= Validation::getOldInput('fechaInscripcion_'.$rowId, $c->getInscripcion()) ?>" form="form-<?= $rowId ?>">
                                </td>
                                <td data-label="Estado">
                                    <?php $error = Validation::getError('estado_'.$rowId); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                    <select name="estado" form="form-<?= $rowId ?>">
                                        <option value="1" <?= Validation::getOldInput('estado_'.$rowId, $c->getActivo())==1?'selected':'' ?>>Activo</option>
                                        <option value="0" <?= Validation::getOldInput('estado_'.$rowId, $c->getActivo())==0?'selected':'' ?>>Inactivo</option>
                                    </select>
                                </td>
                                <td data-label="Imagen">
                                    <?php
                                    $img = $imageManager->getImagesByIds($c->getTbclienteImagenId());
                                    if (!empty($img)) {
                                        $src = '..' . htmlspecialchars($img[0]['tbimagenruta']) . '?t=' . time();
                                        echo '<div class="image-container"><img src="'.$src.'" alt="Imagen" onerror="this.onerror=null; this.src=\'noimage.svg\';"><button type="submit" name="delete_image" value="'.$c->getTbclienteImagenId().'" onclick="return confirm(\'¿Eliminar esta imagen?\');" form="form-'.$rowId.'"><i class="ph ph-x"></i></button></div>';
                                    } else {
                                        echo '<input type="file" name="tbclienteimagenid[]" form="form-'.$rowId.'">';
                                    }
                                    ?>
                                </td>
                                <td data-label="Acción">
                                    <div class="actions">
                                        <button type="submit" name="actualizar" form="form-<?= $rowId ?>" class="btn-row"><i class="ph ph-pencil-simple"></i></button>
                                        <button type="submit" name="eliminar" onclick="return confirm('¿Estás seguro de eliminar este cliente?');" form="form-<?= $rowId ?>" class="btn-row btn-danger"><i class="ph ph-trash"></i></button>
                                    </div>
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
                        <div class="form-grid-container">
                            <input type="hidden" name="id" value="<?= $cliente->getId() ?>">
                            <input type="hidden" name="carnet" value="<?= htmlspecialchars($cliente->getCarnet()) ?>">

                            <div class="form-group">
                                <label>Carnet:</label>
                                <p><?= htmlspecialchars($cliente->getCarnet()) ?></p>
                            </div>
                            <div class="form-group">
                                <?php $error = Validation::getError('nombre'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                <label for="nombre_cliente">Nombre:</label>
                                <input type="text" id="nombre_cliente" name="nombre" maxlength="50" value="<?= Validation::getOldInput('nombre', htmlspecialchars($cliente->getNombre())) ?>">
                            </div>
                            <div class="form-group">
                                <?php $error = Validation::getError('fechaNacimiento'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                <label for="fechaNacimiento_cliente">Fecha de nacimiento:</label>
                                <input type="date" id="fechaNacimiento_cliente" name="fechaNacimiento" value="<?= Validation::getOldInput('fechaNacimiento', $cliente->getFechaNacimiento()) ?>">
                            </div>
                            <div class="form-group">
                                <?php $error = Validation::getError('telefono'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                <label for="telefono_cliente">Teléfono:</label>
                                <input type="text" id="telefono_cliente" name="telefono" maxlength="8" value="<?= Validation::getOldInput('telefono', htmlspecialchars($cliente->getTelefono())) ?>">
                            </div>
                            <div class="form-group">
                                <?php $error = Validation::getError('correo'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                <label for="correo_cliente">Correo:</label>
                                <input type="email" id="correo_cliente" name="correo" maxlength="100" value="<?= Validation::getOldInput('correo', htmlspecialchars($cliente->getCorreo())) ?>">
                            </div>
                                                    <div class="form-group">
                                                        <?php $error = Validation::getError('contrasena'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                                        <label for="contrasena_cliente">Contraseña:</label>
                                                        <div class="password-group">
                                                            <input type="password" id="contrasena_cliente" name="contrasena" maxlength="8" value="<?= Validation::getOldInput('contrasena', htmlspecialchars($cliente->getContrasena())) ?>">
                                                            <i class="ph ph-eye" id="togglePasswordCliente"></i>
                                                        </div>
                                                    </div>                            <div class="form-group">
                                <?php $error = Validation::getError('direccion'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                <label for="direccion_cliente">Dirección:</label>
                                <input type="text" id="direccion_cliente" name="direccion" maxlength="100" value="<?= Validation::getOldInput('direccion', htmlspecialchars($cliente->getDireccion())) ?>">
                            </div>
                            <div class="form-group">
                                <?php $error = Validation::getError('genero'); if ($error): ?><span class="error-message"><?= $error ?></span><?php endif; ?>
                                <label for="genero_cliente">Género:</label>
                                <select id="genero_cliente" name="genero">
                                    <option value="M" <?= (Validation::getOldInput('genero', $cliente->getGenero())=='M'?'selected':'') ?>>Masculino</option>
                                    <option value="F" <?= (Validation::getOldInput('genero', $cliente->getGenero())=='F'?'selected':'') ?>>Femenino</option>
                                    <option value="Otro" <?= (Validation::getOldInput('genero', $cliente->getGenero())=='Otro'?'selected':'') ?>>Otro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fechaInscripcion_cliente">Fecha de inscripción:</label>
                                <input type="date" id="fechaInscripcion_cliente" name="fechaInscripcion" value="<?= Validation::getOldInput('fechaInscripcion', $cliente->getInscripcion()) ?>">
                            </div>
                            <input type="hidden" name="estado" value="<?= $cliente->getActivo() ?>">

                            <div class="form-group">
                                <label>Foto de perfil:</label>
                                <?php
                                $img = $imageManager->getImagesByIds($cliente->getTbclienteImagenId());
                                if (!empty($img)) {
                                    $src = '..' . htmlspecialchars($img[0]['tbimagenruta']) . '?t=' . time();
                                    echo '<div class="image-container"><img src="'.$src.'" alt="Imagen" onerror="this.onerror=null; this.src=\'noimage.svg\';"><button type="submit" name="delete_image" value="'.$cliente->getTbclienteImagenId().'" onclick="return confirm(\'¿Eliminar esta imagen?\');"><i class="ph ph-x"></i></button></div>';
                                } else {
                                    echo '<input type="file" name="tbclienteimagenid[]">';
                                }
                                ?>
                            </div>
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
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#contrasena');

    if (togglePassword) {
        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('ph-eye-slash');
        });
    }

    const togglePasswordCliente = document.querySelector('#togglePasswordCliente');
    const passwordCliente = document.querySelector('#contrasena_cliente');

    if (togglePasswordCliente) {
        togglePasswordCliente.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = passwordCliente.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordCliente.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('ph-eye-slash');
        });
    }
</script>
</body>
</html>