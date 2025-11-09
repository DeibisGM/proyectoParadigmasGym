<?php
session_start();
include_once '../business/salaReservasBusiness.php';
include_once '../business/salaBusiness.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("location: ../view/loginView.php?error=unauthorized");
    exit();
}

$salaReservasBusiness = new SalaReservasBusiness();
$reservas = $salaReservasBusiness->getAllReservasDeSalas();

$salaBusiness = new SalaBusiness();
$todasLasSalas = $salaBusiness->obtenerTbsala();

$mapaSalas = [];
foreach ($todasLasSalas as $sala) {
    $mapaSalas[$sala->getTbsalaid()] = $sala->getTbsalanombre();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocupación de Salas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2><i class="ph ph-calendar-check"></i>Ocupación de Salas por Eventos</h2>
        </header>

        <main>
            <section>
                <h3><i class="ph ph-list-bullets"></i>Calendario de Reservas</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
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
                                        <td data-label="Fecha">
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($reserva['tbreservafecha']))) ?>
                                        </td>
                                        <td data-label="Horario">
                                            <?= htmlspecialchars(date('h:i A', strtotime($reserva['tbreservahorainicio']))) . ' - ' . htmlspecialchars(date('h:i A', strtotime($reserva['tbreservahorafin']))) ?>
                                        </td>
                                        <td data-label="Salas Reservadas">
                                            <?php
                                            $salaIds = explode('$', $reserva['tbsalaid']);
                                            $nombresSalas = [];
                                            foreach ($salaIds as $id) {
                                                $trimmedId = trim($id);
                                                if (isset($mapaSalas[$trimmedId])) {
                                                    $nombresSalas[] = htmlspecialchars($mapaSalas[$trimmedId]);
                                                }
                                            }
                                            echo implode(', ', $nombresSalas);
                                            ?>
                                        </td>
                                        <td data-label="Evento">
                                            <?= htmlspecialchars($reserva['tbeventonombre']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy;
                <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.
            </p>
        </footer>
    </div>
</body>

</html>