<?php
session_start();
include_once '../business/instructorBusiness.php';

$instructorBusiness = new InstructorBusiness();
$instructores = $instructorBusiness->getAllTBInstructor(true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Depuración de Instructores</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="instructorView.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <div class="title-group">
            <h2><i class="ph ph-bug"></i>Panel de Depuración de Instructores</h2>
            <p class="title-subtitle">Vista rápida del estado actual de los instructores registrados.</p>
        </div>
    </header>

    <main>
        <section>
            <h3><i class="ph ph-list-magnifying-glass"></i>Información de instructores</h3>
            <?php if (empty($instructores)): ?>
                <p>No hay instructores registrados.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($instructores as $inst): ?>
                            <tr>
                                <td><?= str_pad($inst->getInstructorId(), 3, '0', STR_PAD_LEFT); ?></td>
                                <td><?= htmlspecialchars($inst->getInstructorNombre()); ?></td>
                                <td><?= htmlspecialchars($inst->getInstructorTelefono()); ?></td>
                                <td>
                                    <?php if ($inst->getInstructorActivo()): ?>
                                        <span class="status-pill success"><i class="ph ph-check-circle"></i>Activo</span>
                                    <?php else: ?>
                                        <span class="status-pill error"><i class="ph ph-warning"></i>Inactivo</span>
                                    <?php endif; ?>
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
