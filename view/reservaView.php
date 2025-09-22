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
$todosLosClientes = [];
if ($tipoUsuario === 'cliente') {
    $misReservas = $reservaBusiness->getTodasMisReservas($usuarioId);
} else {
    // Lógica futura para admin/instructor para ver todas las reservas
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
        <h2><i class="ph ph-calendar-check"></i>
            <?php echo ($tipoUsuario === 'cliente') ? "Mis Reservas" : "Gestión de Reservas"; ?>
        </h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

    <main>
        <?php if ($tipoUsuario === 'cliente'): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Realizar una nueva reserva</h3>
                <p>Para reservar un evento especial o un espacio de uso libre, por favor dirígete a las secciones correspondientes desde el menú principal:</p>
                <div class="menu-grid">
                    <a href="eventoGestionView.php"><button><i class="ph ph-sparkle"></i> Ver y Reservar Eventos</button></a>
                    <a href="horarioLibreView.php"><button><i class="ph ph-barbell"></i> Reservar Uso Libre</button></a>
                </div>
            </section>

            <section>
                <h3><i class="ph ph-list-checks"></i>Mi Historial de Reservas</h3>
                <div id="mis-reservas-list">
                    <?php if (empty($misReservas)): ?>
                        <p>No tienes reservas registradas.</p>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table>
                                <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($misReservas as $reserva): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['hora']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['nombre']); ?></td>
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
            <p>La vista general de reservas para administradores e instructores se mostrará aquí.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>