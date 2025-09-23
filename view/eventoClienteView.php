<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: ../view/loginView.php?error=unauthorized");
    exit();
}

include_once '../business/eventoBusiness.php';
$eventoBusiness = new EventoBusiness();
$eventos = $eventoBusiness->getAllEventosActivos(); // Assuming this method exists or I will create it.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Eventos</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .evento-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            background-color: #f9f9f9;
        }
        .evento-card h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-sparkle"></i> Eventos Especiales</h2>
        <a href="reservaView.php"><i class="ph ph-arrow-left"></i> Volver a Mis Reservas</a>
    </header>

    <main>
        <h3>¡Participa en nuestros eventos!</h3>
        <p>Aquí puedes ver los próximos eventos y reservar tu lugar.</p>

        <?php if (isset($_GET['success'])): ?>
            <p class="success">¡Reserva realizada con éxito!</p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error">Error: <?= htmlspecialchars(urldecode($_GET['error'])) ?></p>
        <?php endif; ?>

        <div class="eventos-list">
            <?php if (empty($eventos)): ?>
                <p>No hay eventos disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($eventos as $evento): ?>
                    <div class="evento-card">
                        <h3><?php echo htmlspecialchars($evento->getNombre()); ?></h3>
                        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($evento->getFecha()); ?></p>
                        <p><strong>Horario:</strong> <?php echo htmlspecialchars(date('g:i a', strtotime($evento->getHoraInicio()))) . ' - ' . htmlspecialchars(date('g:i a', strtotime($evento->getHoraFin()))); ?></p>
                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($evento->getInstructorNombre()); ?></p>
                        <p><strong>Cupos disponibles:</strong> <?php echo $evento->getAforo() - $evento->getReservasCount(); // Assuming this method exists ?></p>
                        <p><?php echo nl2br(htmlspecialchars($evento->getDescripcion())); ?></p>

                        <form action="../action/reservaAction.php" method="POST">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="eventoId" value="<?php echo $evento->getId(); ?>">
                            <button type="submit" <?php echo ($evento->getAforo() - $evento->getReservasCount() <= 0) ? 'disabled' : ''; ?>>
                                <i class="ph ph-ticket"></i> <?php echo ($evento->getAforo() - $evento->getReservasCount() <= 0) ? 'Agotado' : 'Reservar'; ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
