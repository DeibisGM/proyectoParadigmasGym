<?php
require_once '../business/instructorBusiness.php';

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
        <div class="title-group">
            <h2><i class="ph ph-users-three"></i>Instructores disponibles</h2>
            <p class="title-subtitle">Consulta el equipo y encuentra a la persona ideal para tu entrenamiento.</p>
        </div>
    </header>

    <main>
        <section>
            <h3><i class="ph ph-table"></i>Listado general</h3>
            <?php if (empty($instructores)): ?>
                <p>No hay instructores disponibles en este momento.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
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
                                <td><?= htmlspecialchars($instructor->getInstructorNombre()); ?></td>
                                <td><?= htmlspecialchars($instructor->getInstructorTelefono()); ?></td>
                                <td><?= htmlspecialchars($instructor->getInstructorDireccion()); ?></td>
                                <td><?= htmlspecialchars($instructor->getInstructorCorreo()); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section>
            <h3><i class="ph ph-handshake"></i>Conoce a nuestro equipo</h3>
            <?php if (empty($instructores)): ?>
                <p>Estamos trabajando para sumar nuevos instructores. ¡Vuelve pronto!</p>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($instructores as $instructor): ?>
                        <article class="card">
                            <h4><i class="ph ph-identification-card"></i><?= htmlspecialchars($instructor->getInstructorNombre()); ?></h4>
                            <p><strong>Certificación destacada:</strong> <?= $instructor->getInstructorCertificado() ? htmlspecialchars($instructor->getInstructorCertificado()) : 'Disponible para diversas rutinas'; ?></p>
                            <p><strong>Contacto:</strong> <?= htmlspecialchars($instructor->getInstructorCorreo()); ?></p>
                            <p><strong>Teléfono:</strong> <?= htmlspecialchars($instructor->getInstructorTelefono()); ?></p>
                            <p><strong>Dirección:</strong> <?= htmlspecialchars($instructor->getInstructorDireccion()); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
