<?php
session_start();
include_once '../business/salaReservasBusiness.php';
include_once '../business/salaBusiness.php'; // Para obtener los nombres de las salas

// 1. Verificación de sesión y permisos de administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("location: ../view/loginView.php?error=unauthorized");
    exit();
}

// 2. Obtención de datos de la capa de negocio
$salaReservasBusiness = new SalaReservasBusiness();
$reservas = $salaReservasBusiness->getAllReservasDeSalas();

$salaBusiness = new SalaBusiness();
$todasLasSalas = $salaBusiness->getAllSalas();

// 3. Crear un mapa de ID de sala -> nombre de sala para una búsqueda eficiente
$mapaSalas = [];
foreach ($todasLasSalas as $sala) {
    $mapaSalas[$sala->getTbsalaid()] = $sala->getTbsalanombre();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ocupación de Salas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-calendar-check"></i>Ocupación de Salas por Eventos</h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

    <main>
        <section>
            <h3><i class="ph ph-list-bullets"></i>Calendario de Reservas</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Salas Reservadas</th>
                        <th>Evento</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($reservas)): ?>
                        <tr>
                            <td colspan="4">No hay ninguna sala reservada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($reserva['tbreservafecha']))) ?></td>
                                <td><?= htmlspecialchars(date('h:i A', strtotime($reserva['tbreservahorainicio']))) . ' - ' . htmlspecialchars(date('h:i A', strtotime($reserva['tbreservahorafin']))) ?></td>
                                <td>
                                    <?php
                                    $salaIds = explode('$', $reserva['tbsalaid']);
                                    $nombresSalas = [];
                                    foreach ($salaIds as $id) {
                                        $trimmedId = trim($id); // Limpiar espacios por si acaso
                                        if (isset($mapaSalas[$trimmedId])) {
                                            $nombresSalas[] = htmlspecialchars($mapaSalas[$trimmedId]);
                                        }
                                    }
                                    echo implode(', ', $nombresSalas);
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($reserva['tbeventonombre']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>