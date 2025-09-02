<?php
session_start();

// Redirige si el usuario no ha iniciado sesión
if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

// Determinar roles y permisos
$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
$esInstructor = ($_SESSION['tipo_usuario'] === 'instructor');
$usuarioIdSesion = $_SESSION['usuario_id'] ?? null;

// Incluir las clases necesarias
include_once '../business/certificadoBusiness.php';
include_once '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';

// Inicializar las clases de negocio
$certificadoBusiness = new CertificadoBusiness();
$instructorBusiness = new InstructorBusiness();
$imageManager = new ImageManager();

// Obtener la lista de instructores
$instructores = $instructorBusiness->getAllTBInstructor($esAdmin);
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
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-users-three"></i>Gestión de Instructores</h2>

    </header>

    <main>
        <?php
        if (isset($_GET['success'])) {
            $message = '';
            switch ($_GET['success']) {
                case 'created':
                    $message = 'Instructor creado exitosamente.';
                    break;
                case 'updated':
                    $message = 'Instructor actualizado exitosamente.';
                    break;
                case 'deleted':
                    $message = 'Instructor desactivado exitosamente.';
                    break;
                case 'activated':
                    $message = 'Instructor activado exitosamente.';
                    break;
                case 'image_deleted':
                    $message = 'Imagen eliminada exitosamente.';
                    break;
                default:
                    $message = 'Operación exitosa.';
                    break;
            }
            echo '<div class="success-message">' . htmlspecialchars($message) . '</div>';
        }

        if (isset($_GET['error'])) {
            $message = '';
            switch ($_GET['error']) {
                case 'dbError':
                    $message = 'Error en la base de datos. Intente de nuevo.';
                    break;
                case 'notFound':
                    $message = 'Instructor no encontrado.';
                    break;
                case 'error':
                    $message = 'Ocurrió un error inesperado.';
                    break;
                case 'invalidRequest':
                    $message = 'Solicitud inválida.';
                    break;
                default:
                    // For custom validation messages from business layer
                    $message = urldecode(htmlspecialchars($_GET['error']));
                    break;
            }
            echo '<div class="error-message">' . htmlspecialchars($message) . '</div>';
        }
        ?>
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
                        // Lógica para determinar si el usuario actual puede editar esta fila
                        $esPropietario = $esInstructor && $instructor->getInstructorId() == $usuarioIdSesion;
                        $puedeEditar = $esAdmin || $esPropietario;
                        $formId = "form-instructor-" . htmlspecialchars($instructor->getInstructorId());
                        ?>
                        <tr>
                            <!-- Célula de Cédula (solo visualización) -->
                            <td><?php echo htmlspecialchars($instructor->getInstructorId()); ?></td>

                            <!-- Campos de entrada asociados al formulario de la fila mediante el atributo 'form' -->
                            <td>
                                <input form="<?php echo $formId; ?>" type="text" name="nombre"
                                       value="<?php echo htmlspecialchars($instructor->getInstructorNombre()); ?>"
                                       placeholder="Nombre" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                            </td>
                            <td>
                                <input form="<?php echo $formId; ?>" type="text" name="telefono"
                                       value="<?php echo htmlspecialchars($instructor->getInstructorTelefono()); ?>"
                                       placeholder="Teléfono" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                            </td>
                            <td>
                                <input form="<?php echo $formId; ?>" type="email" name="correo"
                                       value="<?php echo htmlspecialchars($instructor->getInstructorCorreo()); ?>"
                                       placeholder="Correo" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                            </td>
                            <td>
                                <input form="<?php echo $formId; ?>" type="password" name="contraseña"
                                       value="<?php echo htmlspecialchars($instructor->getInstructorContraseña()); ?>"
                                       placeholder="Contraseña" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                            </td>
                            <td>
                                <?php
                                $imagen = $imageManager->getImagesByIds($instructor->getTbinstructorImagenId());
                                if (!empty($imagen)) {
                                    echo '<div class="image-container"><img src="..' . htmlspecialchars($imagen[0]['tbimagenruta']) . '?t=' . time() . '" alt="Imagen">';
                                    if ($puedeEditar) {
                                        // El botón para borrar la imagen también debe estar asociado al formulario
                                        echo '<button form="' . $formId . '" type="submit" name="delete_image" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button>';
                                    }
                                    echo '</div>';
                                } else {
                                    if ($puedeEditar) {
                                        echo '<input form="' . $formId . '" type="file" name="tbinstructorimagenid[]">';
                                    } else {
                                        echo 'Sin imagen';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $certificados = $instructor->getInstructorCertificado(); // Get certificates directly from the Instructor object
                                $nombresCertificados = [];
                                foreach ($certificados as $certificado) {
                                    $nombresCertificados[] = $certificado->getNombre();
                                }
                                echo !empty($nombresCertificados) ? implode('<br>', array_map('htmlspecialchars', $nombresCertificados)) : "N/A";
                                ?>
                                <br><a href="certificadoView.php?instructor_id=<?php echo $instructor->getInstructorId(); ?>">Ver/Editar</a>
                            </td>
                            <td class="actions-cell">
                                <!-- La celda de acciones siempre contiene un formulario, garantizando la consistencia estructural -->
                                <form id="<?php echo $formId; ?>" method="post" action="../action/instructorAction.php"
                                      enctype="multipart/form-data"
                                      style="display: flex; gap: 0.5rem; min-height: 40px; align-items: center;">
                                    <input type="hidden" name="id"
                                           value="<?php echo $instructor->getInstructorId(); ?>">
                                    <input type="hidden" name="cuenta"
                                           value="<?php echo htmlspecialchars($instructor->getInstructorCuenta() ?? ''); ?>">
                                    <input type="hidden" name="direccion"
                                           value="<?php echo htmlspecialchars($instructor->getInstructorDireccion() ?? ''); ?>">

                                    <?php // Los botones se renderizan condicionalmente DENTRO del formulario
                                    ?>
                                    <?php if ($puedeEditar): ?>
                                        <button type="submit" name="update" title="Actualizar"><i
                                                    class="ph ph-floppy-disk"></i> Actualizar
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($esAdmin): ?>
                                        <?php if ($instructor->getInstructorActivo()): ?>
                                            <button type="submit" name="delete"
                                                    onclick="return confirm('¿Desactivar este instructor?');"
                                                    title="Desactivar"><i class="ph ph-toggle-right"></i> Desactivar
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="activate" title="Activar"><i
                                                        class="ph ph-toggle-left"></i> Activar
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </form>
                            </td>
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