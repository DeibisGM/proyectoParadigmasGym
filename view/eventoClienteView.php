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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Eventos</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="reservaView.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Eventos Disponibles</h2>
        </header>

        <main>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message flash-msg">¡Reserva realizada con éxito!</p>
            <?php elseif (isset($_GET['error'])): ?>
                <p class="error-message flash-msg">Error:
                    <?= htmlspecialchars(urldecode($_GET['error'])) ?>
                </p>
            <?php endif; ?>

            <?php if (empty($eventos)): ?>
                <section>
                    <p>No hay eventos disponibles en este momento.</p>
                </section>
            <?php else: ?>
                <?php foreach ($eventos as $evento):
                    $cuposDisponibles = $evento->getAforo() - $evento->getReservasCount();
                    ?>
                    <section class="evento-card">
                        <h3 style="display: flex; justify-content: space-between; align-items: center;">
                            <?php echo htmlspecialchars($evento->getNombre()); ?>
                            <span class="badge-soft <?php echo $evento->getTipo() == 'abierto' ? 'abierto' : 'cerrado'; ?>">
                                <?php echo ucfirst($evento->getTipo()); ?>
                            </span>
                        </h3>
                        <p><strong>Fecha:</strong>
                            <?php echo htmlspecialchars($evento->getFecha()); ?> |
                            <strong>Horario:</strong>
                            <?php echo htmlspecialchars(date('g:i a', strtotime($evento->getHoraInicio()))) . ' - ' . htmlspecialchars(date('g:i a', strtotime($evento->getHoraFin()))); ?>
                        </p>
                        <p><strong>Instructor:</strong>
                            <?php echo htmlspecialchars($evento->getInstructorNombre()); ?> |
                            <strong>Cupos disponibles:</strong>
                            <?php echo $cuposDisponibles; ?>
                        </p>
                        <p>
                            <?php echo nl2br(htmlspecialchars($evento->getDescripcion())); ?>
                        </p>

                        <hr>

                        <form action="../action/reservaAction.php" method="POST">
                            <h4><i class="ph ph-ticket"></i> Realizar Reserva</h4>
                            <input type="hidden" name="action" value="create_evento">
                            <input type="hidden" name="eventoId" value="<?php echo $evento->getId(); ?>">

                            <div class="form-grid-container">
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label style="flex-direction: row; align-items: center;">
                                        <input type="checkbox" name="incluirme" checked
                                            style="width: auto; height: auto; margin-right: 0.5rem;">
                                        Incluirme en la reserva
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="ids_invitados_<?php echo $evento->getId(); ?>">IDs de
                                        miembros:</label>
                                    <input type="text" name="ids_invitados"
                                        id="ids_invitados_<?php echo $evento->getId(); ?>"
                                        placeholder="Ej: 2, 8, 15">
                                </div>
                                <?php if ($evento->getTipo() === 'abierto'): ?>
                                    <div class="form-group">
                                        <label for="invitados_anonimos_<?php echo $evento->getId(); ?>">Invitados no
                                            miembros:</label>
                                        <input type="number" name="invitados_anonimos"
                                            id="invitados_anonimos_<?php echo $evento->getId(); ?>" min="0" value="0">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" <?php echo ($cuposDisponibles <= 0) ? 'disabled' : ''; ?>
                                style="margin-top: 1rem;">
                                <i class="ph ph-ticket"></i>
                                <?php echo ($cuposDisponibles <= 0) ? 'Agotado' : 'Confirmar Reserva(s)'; ?>
                            </button>
                        </form>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>