<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');
$esInstructor = ($_SESSION['tipo_usuario'] === 'instructor');
$instructorIdSesion = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

// Usar include_once para evitar errores de re-declaración
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
    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }

        .image-container img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .delete-image-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            padding: 3px 6px;
            border-radius: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<header>
    <h2>Gimnasio - Instructores</h2>
    <a href="../index.php">Volver al Inicio</a>
</header>
<hr>
<main>
    <?php if ($esAdmin): ?>
        <h2>Crear Nuevo Instructor</h2>
        <form method="post" action="../action/instructorAction.php" enctype="multipart/form-data">
            <table border="1">
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
                    <td><input type="text" name="id" required></td>
                    <td><input type="text" name="nombre" required></td>
                    <td><input type="text" name="telefono"></td>
                    <td><input type="email" name="correo" required></td>
                    <td><input type="password" name="contraseña" required></td>
                    <td><input type="file" name="tbinstructorimagenid[]" accept="image/*"></td>
                    <td><input type="submit" value="Crear" name="create"></td>
                    <!-- Campos ocultos que no se usan en la UI simplificada -->
                    <input type="hidden" name="direccion" value="">
                    <input type="hidden" name="cuenta" value="">
                </tr>
                </tbody>
            </table>
        </form>
    <?php endif; ?>

    <h2>Lista de Instructores</h2>
    <table border="1">
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
                    <!-- Campos ocultos con datos que no se editan en la tabla -->
                    <input type="hidden" name="id" value="<?php echo $instructor->getInstructorId(); ?>">
                    <input type="hidden" name="cuenta"
                           value="<?php echo htmlspecialchars($instructor->getInstructorCuenta() ?? ''); ?>">
                    <input type="hidden" name="direccion"
                           value="<?php echo htmlspecialchars($instructor->getInstructorDireccion() ?? ''); ?>">

                    <td><?php echo str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT); ?></td>

                    <!-- Campos editables -->
                    <td><input type="text" name="nombre"
                               value="<?php echo htmlspecialchars($instructor->getInstructorNombre()); ?>" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                    </td>
                    <td><input type="text" name="telefono"
                               value="<?php echo htmlspecialchars($instructor->getInstructorTelefono()); ?>" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                    </td>
                    <td><input type="email" name="correo"
                               value="<?php echo htmlspecialchars($instructor->getInstructorCorreo()); ?>" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                    </td>
                    <td><input type="password" name="contraseña"
                               value="<?php echo htmlspecialchars($instructor->getInstructorContraseña()); ?>" <?php if (!$puedeEditar) echo 'readonly'; ?>>
                    </td>

                    <!-- Celda para la imagen -->
                    <td>
                        <?php
                        $imagen = $imageManager->getImagesByIds($instructor->getTbinstructorImagenId());
                        if (!empty($imagen)) {
                            echo '<div class="image-container">';
                            echo '<img src="..' . htmlspecialchars($imagen[0]['tbimagenruta']) . '?t=' . time() . '" alt="Imagen">';
                            if ($puedeEditar) {
                                echo '<button type="submit" name="delete_image" class="delete-image-btn" onclick="return confirm(\'¿Eliminar esta imagen?\');">X</button>';
                            }
                            echo '</div>';
                        } else {
                            if ($puedeEditar) {
                                echo '<input type="file" name="tbinstructorimagenid[]">';
                            } else {
                                echo 'Sin imagen';
                            }
                        }
                        ?>
                    </td>

                    <!-- Celda de Certificados -->
                    <td>
                        <?php
                        $certificados = $certificadoBusiness->getCertificadosPorInstructor($instructor->getInstructorId());
                        if (!empty($certificados)) {
                            foreach ($certificados as $cert) {
                                echo htmlspecialchars($cert->getNombre()) . "<br>";
                            }
                        } else {
                            echo "N/A";
                        }
                        ?>
                        <a href="certificadoView.php?instructor_id=<?php echo $instructor->getInstructorId(); ?>">Ver/Editar</a>
                    </td>

                    <!-- Celda de Acciones -->
                    <td>
                        <?php if ($puedeEditar): ?>
                            <button type="submit" name="update">Actualizar</button>
                        <?php endif; ?>
                        <?php if ($esAdmin): ?>
                            <?php if ($instructor->getInstructorActivo()): ?>
                                <button type="submit" name="delete"
                                        onclick="return confirm('¿Desactivar este instructor? Su imagen de perfil será eliminada.');">
                                    Desactivar
                                </button>
                            <?php else: ?>
                                <button type="submit" name="activate">Activar</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>