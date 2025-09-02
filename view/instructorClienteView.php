<?php
include_once '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';

$business = new InstructorBusiness();
$imageManager = new ImageManager();
$instructores = $business->getAllTBInstructor();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Instructores</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-chalkboard-teacher"></i>Nuestros Instructores</h2>

    </header>
    <main>
        <section>
            <table>
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
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>