<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Error de autenticación.']);
    exit();
}

include_once '../business/reservaBusiness.php';
include_once '../data/horarioLibreData.php'; // Include the missing data file

$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
$reservaBusiness = new ReservaBusiness();
$horarioLibreData = new HorarioLibreData(); // Instantiate the data class

if (isset($_POST['action']) && $_POST['action'] === 'create') {
    if ($tipoUsuario !== 'cliente') {
        echo json_encode(['success' => false, 'message' => 'Solo los clientes pueden reservar.']);
        exit();
    }

    $eventoId = $_POST['eventoId'] ?? null;
    $horarioLibreId = $_POST['horarioLibreId'] ?? null;
    $horarioLibreIds = $_POST['horarioLibreIds'] ?? null;

    // --- Case 1: Single Event Reservation (from form submission) ---
    if ($eventoId) {
        $resultado = $reservaBusiness->crearReserva($usuarioId, $eventoId, null);
        if ($resultado === true) {
            header("Location: ../view/eventoClienteView.php?success=true");
        } else {
            $errorMessage = urlencode(is_string($resultado) ? $resultado : 'Error al procesar la reserva.');
            header("Location: ../view/eventoClienteView.php?error=" . $errorMessage);
        }
        exit();
    }

    // --- Case 2: Single Free Slot Reservation (from old AJAX call) ---
    if ($horarioLibreId) {
        $resultado = $reservaBusiness->crearReserva($usuarioId, null, $horarioLibreId);
        if ($resultado === true) {
            echo json_encode(['success' => true, 'message' => 'Reserva creada con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => is_string($resultado) ? $resultado : 'Error al procesar la reserva.']);
        }
        exit();
    }

    // --- Case 3: Multiple Free Slot Reservation (new AJAX call) ---
    if ($horarioLibreIds && is_array($horarioLibreIds)) {
        $resultados = $reservaBusiness->crearMultiplesReservasLibre($usuarioId, $horarioLibreIds);

        $successCount = $resultados['success_count'];
        $failureCount = $resultados['failure_count'];
        $message = "Resumen de la operación:\n";
        $message .= "Reservas exitosas: " . $successCount . "\n";
        $message .= "Reservas fallidas: " . $failureCount . "\n\n";

        if ($failureCount > 0) {
            $message .= "Detalles de los fallos:\n";
            foreach($resultados['details'] as $detail) {
                if ($detail['status'] === 'failure') {
                    // We need to get the date/time for the failed ID to show a useful message
                    $horario = $horarioLibreData->getHorarioLibrePorId($detail['id']);
                    $fechaHora = $horario ? $horario->getFecha() . ' a las ' . $horario->getHora() : 'ID ' . $detail['id'];
                    $message .= "- Horario (" . $fechaHora . "): " . $detail['reason'] . "\n";
                }
            }
        }

        echo json_encode([
            'success' => $successCount > 0,
            'message' => $message
        ]);
        exit();
    }
}

// Default response if no case is matched
echo json_encode(['success' => false, 'message' => 'Acción no reconocida o datos insuficientes.']);
?>