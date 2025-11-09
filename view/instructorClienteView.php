<?php
session_start();
include_once '../business/instructorBusiness.php';
$business = new InstructorBusiness();
$instructores = $business->getAllTBInstructor();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructores - Vista Cliente</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Instructores Disponibles</h2>
        </header>

        <main>
            <section>
                <h3><i class="ph ph-list-bullets"></i> Nuestros Instructores</h3>
                <?php if (empty($instructores)): ?>
                    <p>No hay instructores disponibles en este momento.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table-clients">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Correo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($instructores as $instructor): ?>
                                    <tr>
                                        <td data-label="Nombre">
                                            <?php echo htmlspecialchars($instructor->getInstructorNombre()); ?>
                                        </td>
                                        <td data-label="Teléfono">
                                            <?php echo htmlspecialchars($instructor->getInstructorTelefono()); ?>
                                        </td>
                                        <td data-label="Dirección">
                                            <?php echo htmlspecialchars($instructor->getInstructorDireccion()); ?>
                                        </td>
                                        <td data-label="Correo">
                                            <?php echo htmlspecialchars($instructor->getInstructorCorreo()); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>

    </div>
</body>

</html>