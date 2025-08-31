<?php
// Usar include_once
include_once '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';

$business = new InstructorBusiness();
$imageManager = new ImageManager();
$instructores = $business->getAllTBInstructor(); // Solo trae instructores activos por defecto
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructores - Vista Cliente</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
        }

        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
<header>
    <h2>Gimnasio - Nuestros Instructores</h2>
    <a href="../index.php">Volver al Inicio</a>
</header>
<hr>
<main>
    <table border="1">
        <thead>
        <tr>
            <th>Foto</th>
            <th>Nombre</th>
            <th>Correo</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($instructores)): ?>
            <tr>
                <td colspan="3">No hay instructores disponibles en este momento.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($instructores as $instructor): ?>
                <tr>
                    <td>
                        <?php
                        $imagen = $imageManager->getImagesByIds($instructor->getTbinstructorImagenId());
                        if (!empty($imagen)) {
                            echo '<img src="..' . htmlspecialchars($imagen[0]['tbimagenruta']) . '?t=' . time() . '" alt="Foto de ' . htmlspecialchars($instructor->getInstructorNombre()) . '" class="profile-pic">';
                        } else {
                            echo '<img src="../img/default_avatar.png" alt="Sin foto" class="profile-pic">';
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($instructor->getInstructorNombre()); ?></td>
                    <td><?php echo htmlspecialchars($instructor->getInstructorCorreo()); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</main>
<hr>
</body>
</html>