<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
$esInstructor = ($_SESSION['tipo_usuario'] === 'instructor');
$instructorIdSesion = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

include_once '../business/certificadoBusiness.php';
include_once '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';

$certificadoBusiness = new CertificadoBusiness();
$business = new InstructorBusiness();
$imageManager = new ImageManager();

$instructores = $business->getAllTBInstructor($esAdmin);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Instructores</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-users-three"></i>Gestión de Instructores</h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

    <main>
        <?php if ($esAdmin): ?>
            <section>
                <h3><i class="ph ph-user-plus"></i>Crear Nuevo Instructor</h3>
                <form method="post" action="../action/instructorAction.php" enctype="multipart/form-data">
                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Contraseña</th>
                                <th>Imagen</th>
                                <th>Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="text" name="id" placeholder="Cédula" required></td>
                                <td><input type="text" name="nombre" placeholder="Nombre" required></td>
                                <td><input type="text" name="telefono" placeholder="Teléfono"></td>
                                <td><input type="email" name="correo" placeholder="Correo" required></td>
                                <td><input type="password" name="contraseña" placeholder="Contraseña" required></td>
                                <td><input type="file" name="tbinstructorimagenid[]" accept="image/*"></td>
                                <td>
                                    <button type="submit" name="create"><i class="ph ph-plus"></i>Crear</button>
                                </td>
                                <input type="hidden" name="direccion" value="">
                                <input type="hidden" name="cuenta" value="">
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-list-bullets"></i>Lista de Instructores</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Contraseña</th>
                        <th>Imagen</th>
                        <th>Certificados</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($instructores as $instructor):
                        $esPropietario = $esInstructor && $instructor->getInstructorId() == $instructorIdSesion;
                        $puedeEditar = $esAdmin || $esPropietario;
                        ?>
                        <tr>
                            <form method="post" action="../action/instructorAction.php" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $instructor->getInstructorId(); ?>">
                                <input type="hidden" name="cuenta"
                                       value="<?php echo htmlspecialchars($instructor->getInstructorCuenta() ?? ''); ?>">
                                <input type="hidden" name="direccion"
                                       value="<?php echo htmlspecialchars($instructor->getInstructorDireccion() ?? ''); ?>">
                                <td><?php echo str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT); ?></td>
                                <td><input type="text" name="nombre"
                                           value="<?php echo htmlspecialchars($instructor->getInstructorNombre()); ?>" placeholder="Nombre" <?php if (!$puedeEditar) echo 'readonly'; ?>> 
                                </td>
                                <td><input type="text" name="telefono"
                                           value="<?php echo htmlspecialchars($instructor->getInstructorTelefono()); ?>" placeholder="Teléfono" <?php if (!$puedeEditar) echo 'readonly'; ?>> 
                                </td>
                                <td><input type="email" name="correo"
                                           value="<?php echo htmlspecialchars($instructor->getInstructorCorreo()); ?>" placeholder="Correo" <?php if (!$puedeEditar) echo 'readonly'; ?>> 
                                </td>
                                <td><input type="password" name="contraseña"
                                           value="<?php echo htmlspecialchars($instructor->getInstructorContraseña()); ?>" placeholder="Contraseña" <?php if (!$puedeEditar) echo 'readonly'; ?>> 
                                </td>
                                <td>
                                    <?php
                                    $imagen = $imageManager->getImagesByIds($instructor->getTbinstructorImagenId());
                                    if (!empty($imagen)) {
                                        echo '<div class="image-container"><img src="..' . htmlspecialchars($imagen[0]['tbimagenruta']) . '?t=' . time() . '" alt="Imagen">';
                                        if ($puedeEditar) echo '<button type="submit" name="delete_image" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button>';
                                        echo '</div>';
                                    } else {
                                        if ($puedeEditar) echo '<input type="file" name="tbinstructorimagenid[]">';
                                        else echo 'Sin imagen';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $certificados = $certificadoBusiness->getCertificadosPorInstructor($instructor->getInstructorId());
                                    if (!empty($certificados)) {
                                        foreach ($certificados as $cert) echo htmlspecialchars($cert->getNombre()) . "<br>";
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>
                                    <a href="certificadoView.php?instructor_id=<?php echo $instructor->getInstructorId(); ?>">Ver/Editar</a>
                                </td>
                                <td class="actions-cell">
                                    <?php if ($puedeEditar): ?>
                                        <button type="submit" name="update" title="Actualizar"><i class="ph ph-floppy-disk"></i> Actualizar</button>
                                    <?php endif; ?>
                                    <?php if ($esAdmin): ?>
                                        <?php if ($instructor->getInstructorActivo()): ?>
                                            <button type="submit" name="delete"
                                                    onclick="return confirm('¿Desactivar este instructor?');"
                                                    title="Desactivar"><i class="ph ph-toggle-right"></i> Desactivar</button>
                                        <?php else: ?>
                                            <button type="submit" name="activate" title="Activar"><i
                                                        class="ph ph-toggle-left"></i> Activar</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </form>
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