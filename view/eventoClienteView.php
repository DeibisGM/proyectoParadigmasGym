<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: ../view/loginView.php?error=unauthorized");
    exit();
}

include_once '../business/eventoBusiness.php';
$eventoBusiness = new EventoBusiness();
$eventos = $eventoBusiness->getAllEventosActivos();
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tipo-evento {
            font-size: 0.8em;
            padding: 4px 8px;
            border-radius: 12px;
            color: white;
            font-weight: bold;
        }
        .tipo-abierto { background-color: #28a745; }
        .tipo-privado { background-color: #dc3545; }
        .form-reserva-grupo {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <a href="reservaView.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2><i class="ph ph-sparkle"></i> Eventos Disponibles</h2>
    </header>

    <main>
        <h3>¡Participa en nuestros eventos!</h3>
        <p>Aquí puedes ver los próximos eventos y reservar tu lugar, e incluso el de tus amigos y familiares.</p>

        <?php if (isset($_GET['success'])): ?>
            <p class="success">¡Reserva realizada con éxito!</p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error">Error: <?= htmlspecialchars(urldecode($_GET['error'])) ?></p>
        <?php endif; ?>

        <div class="eventos-list">
            <?php if (empty($eventos)): ?>
                <p>No hay eventos disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($eventos as $evento):
                    $cuposDisponibles = $evento->getAforo() - $evento->getReservasCount();
                    ?>
                    <div class="evento-card">
                        <h3>
                            <?php echo htmlspecialchars($evento->getNombre()); ?>
                            <span class="tipo-evento tipo-<?php echo $evento->getTipo(); ?>">
                                <?php echo ucfirst($evento->getTipo()); ?>
                            </span>
                        </h3>
                        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($evento->getFecha()); ?></p>
                        <p><strong>Horario:</strong> <?php echo htmlspecialchars(date('g:i a', strtotime($evento->getHoraInicio()))) . ' - ' . htmlspecialchars(date('g:i a', strtotime($evento->getHoraFin()))); ?></p>
                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($evento->getInstructorNombre()); ?></p>
                        <p><strong>Cupos disponibles:</strong> <?php echo $cuposDisponibles; ?></p>
                        <p><?php echo nl2br(htmlspecialchars($evento->getDescripcion())); ?></p>

                        <!-- MODIFICADO: Formulario para usar IDs de cliente en lugar de carnets -->
                        <form action="../action/reservaAction.php" method="POST" class="form-reserva-grupo">
                            <input type="hidden" name="action" value="create_evento">
                            <input type="hidden" name="eventoId" value="<?php echo $evento->getId(); ?>">

                            <h4><i class="ph ph-users"></i> Realizar Reserva</h4>

                            <div class="form-group">
                                <label><input type="checkbox" name="incluirme" checked> Incluirme en la reserva</label>
                            </div>

                            <div class="form-group">
                                <label for="ids_invitados_<?php echo $evento->getId(); ?>">Reservar para otros miembros (ingrese IDs de cliente separados por coma):</label>
                                <input type="text" name="ids_invitados" id="ids_invitados_<?php echo $evento->getId(); ?>" placeholder="Ej: 2, 8, 15">
                            </div>

                            <?php if ($evento->getTipo() === 'abierto'): ?>
                                <div class="form-group">
                                    <label for="invitados_anonimos_<?php echo $evento->getId(); ?>">Número de invitados no miembros (solo para eventos abiertos):</label>
                                    <input type="number" name="invitados_anonimos" id="invitados_anonimos_<?php echo $evento->getId(); ?>" min="0" value="0" style="width: 100px;">
                                </div>
                            <?php endif; ?>

                            <button type="submit" <?php echo ($cuposDisponibles <= 0) ? 'disabled' : ''; ?>>
                                <i class="ph ph-ticket"></i> <?php echo ($cuposDisponibles <= 0) ? 'Agotado' : 'Confirmar Reserva(s)'; ?>
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