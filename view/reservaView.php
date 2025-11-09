<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$usuarioId = $_SESSION['usuario_id'];

include_once '../business/reservaBusiness.php';
$reservaBusiness = new ReservaBusiness();

$misReservas = [];
$todasLasReservas = [];
if ($tipoUsuario === 'cliente') {
    $misReservas = $reservaBusiness->getTodasMisReservas($usuarioId);
} else {
    $todasLasReservas = $reservaBusiness->getAllReservas();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2>
            <?php echo ($tipoUsuario === 'cliente') ? "Mis Reservas" : "Gestión de Reservas"; ?>
        </h2>
    </header>

    <main>
        <?php if ($tipoUsuario === 'cliente'): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Realizar una nueva reserva</h3>
                <p>Para reservar un evento especial, un espacio de uso libre o una sesión con instructor personal, por favor dirígete a las secciones correspondientes:</p>
                <div class="menu-grid">
                    <a href="horarioPersonalView.php"><button><i class="ph ph-user-plus"></i> Instructor Personal</button></a>
                    <a href="eventoClienteView.php"><button><i class="ph ph-sparkle"></i> Ver y Reservar Eventos</button></a>
                    <a href="horarioLibreView.php"><button><i class="ph ph-barbell"></i> Reservar Uso Libre</button></a>
                </div>
            </section>

            <section>
                <h3><i class="ph ph-list-checks"></i>Mi Historial de Reservas</h3>
                <div id="mis-reservas-list">
                    <?php if (empty($misReservas)): ?>
                        <p>No tienes reservas registradas.</p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-clients">
                                <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Instructor</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($misReservas as $reserva): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['hora']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['descripcion']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['instructor']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['estado']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php else: ?>
            <section>
                <h3><i class="ph ph-list-checks"></i>Historial de Todas las Reservas</h3>

                <?php if ($tipoUsuario === 'admin'): ?>
                    <div class="menu-grid" style="margin-bottom: 20px;">
                        <a href='horarioPersonalView.php'><button><i class="ph ph-user-plus"></i>Gestionar Instructor Personal</button></a>
                        <a href='eventoGestionView.php'><button><i class="ph ph-calendar-plus"></i>Gestionar Eventos</button></a>
                        <a href='horarioLibreView.php'><button><i class="ph ph-clock-afternoon"></i>Gestionar Horario Libre</button></a>
                    </div>
                <?php endif; ?>

                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cliente Asistente</th>
                            <th>Reservado Por</th> <!-- NUEVO -->
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Instructor</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($todasLasReservas)): ?>
                            <tr>
                                <td colspan="8">No hay ninguna reserva registrada en el sistema.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todasLasReservas as $reserva): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['hora']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['cliente']); ?></td>
                                    <!-- NUEVO: Mostrar responsable -->
                                    <td><?php echo htmlspecialchars($reserva['responsable'] ?? $reserva['cliente']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['instructor']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['estado']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>