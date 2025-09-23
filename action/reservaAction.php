<?php
session_start();
ob_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error de autenticación.']);
    exit();
}

include_once '../business/reservaBusiness.php';

$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
$reservaBusiness = new ReservaBusiness();

if (isset($_POST['action']) && $_POST['action'] === 'create') {
    if ($tipoUsuario !== 'cliente') {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Solo los clientes pueden reservar.']);
        exit();
    }

    $eventoId = $_POST['eventoId'] ?? null;
    $horarioLibreId = $_POST['horarioLibreId'] ?? null;
    $horarioLibreIds = $_POST['horarioLibreIds'] ?? null;

    if ($eventoId) {
        $resultado = $reservaBusiness->crearReserva($usuarioId, $eventoId, null);
        ob_end_clean();
        if ($resultado === true) {
            header("Location: ../view/eventoClienteView.php?success=true");
        } else {
            $errorMessage = urlencode(is_string($resultado) ? $resultado : 'Error al procesar la reserva.');
            header("Location: ../view/eventoClienteView.php?error=" . $errorMessage);
        }
        exit();
    }

    if ($horarioLibreId) {
        $resultado = $reservaBusiness->crearReserva($usuarioId, null, $horarioLibreId);
        ob_clean();
        if ($resultado === true) {
            echo json_encode(['success' => true, 'message' => 'Reserva creada con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => is_string($resultado) ? $resultado : 'Error al procesar la reserva.']);
        }
        exit();
    }

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
                    $horario = $reservaBusiness->getHorarioLibrePorId($detail['id']);
                    $fechaHora = $horario ? $horario->getFecha() . ' a las ' . $horario->getHora() : 'ID ' . $detail['id'];
                    $message .= "- Horario (" . $fechaHora . "): " . $detail['reason'] . "\n";
                }
            }
        }
        ob_clean();
        echo json_encode([
            'success' => $successCount > 0,
            'message' => $message
        ]);
        exit();
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'cancel_libre') {
    if ($tipoUsuario !== 'cliente') {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Acción no permitida.']);
        exit();
    }

    $reservaId = $_POST['reservaId'] ?? null;
    if (!$reservaId) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'ID de reserva no proporcionado.']);
        exit();
    }

    $resultado = $reservaBusiness->cancelarReservaLibre($reservaId, $usuarioId);
    ob_clean();
    if ($resultado === true) {
        echo json_encode(['success' => true, 'message' => 'Reserva cancelada con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => is_string($resultado) ? $resultado : 'Error al cancelar la reserva.']);
    }
    exit();
}

ob_clean();
echo json_encode(['success' => false, 'message' => 'Acción no reconocida o datos insuficientes.']);