<?php
session_start();
ob_start(); // Iniciar buffer de salida

// NO establecer el header de JSON aquí globalmente

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de autenticación.']);
    exit();
}

include_once '../business/reservaBusiness.php';

$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
$reservaBusiness = new ReservaBusiness();

if (isset($_POST['action']) && $_POST['action'] === 'create_evento') {
    ob_end_clean(); // Limpiar buffer antes de la redirección
    if ($tipoUsuario !== 'cliente') {
        header("Location: ../view/eventoClienteView.php?error=" . urlencode('Solo los clientes pueden reservar.'));
        exit();
    }

    $eventoId = $_POST['eventoId'] ?? null;
    $incluirme = isset($_POST['incluirme']);
    $idsInvitados = trim($_POST['ids_invitados'] ?? '');
    $invitadosAnonimos = (int)($_POST['invitados_anonimos'] ?? 0);

    if (!$eventoId) {
        header("Location: ../view/eventoClienteView.php?error=" . urlencode('ID de evento no proporcionado.'));
        exit();
    }

    $resultado = $reservaBusiness->crearReservaEventoAgrupada($usuarioId, $eventoId, $incluirme, $idsInvitados, $invitadosAnonimos);

    if ($resultado === true) {
        header("Location: ../view/reservaView.php?success=evento_reservado");
    } else {
        $errorMessage = urlencode(is_string($resultado) ? $resultado : 'Error al procesar la reserva.');
        header("Location: ../view/eventoClienteView.php?error=" . $errorMessage . "&eventoId=" . $eventoId);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'create_libre') {
    header('Content-Type: application/json'); // MODIFICADO: Header movido aquí
    ob_end_clean();

    if ($tipoUsuario !== 'cliente') {
        echo json_encode(['success' => false, 'message' => 'Solo los clientes pueden reservar.']);
        exit();
    }

    $horarioLibreIds = $_POST['horarioLibreIds'] ?? [];
    $incluirme = isset($_POST['incluirme']);
    $idsInvitados = trim($_POST['ids_invitados'] ?? '');

    $resultados = $reservaBusiness->crearReservaLibreAgrupada($usuarioId, $horarioLibreIds, $incluirme, $idsInvitados);

    $successCount = $resultados['success_count'];
    $failureCount = $resultados['failure_count'];
    $message = "Resumen de la operación:\n";
    $message .= "Reservas exitosas: " . $successCount . "\n";
    $message .= "Reservas fallidas: " . $failureCount . "\n\n";

    if ($failureCount > 0) {
        $message .= "Detalles de los fallos:\n";
        foreach ($resultados['details'] as $detail) {
            if ($detail['status'] === 'failure') {
                $horario = $reservaBusiness->getHorarioLibrePorId($detail['id']);
                $fechaHora = $horario ? $horario->getFecha() . ' a las ' . date('H:i', strtotime($horario->getHora())) : 'ID ' . $detail['id'];
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

if (isset($_POST['action']) && $_POST['action'] === 'cancel_libre') {
    header('Content-Type: application/json'); // MODIFICADO: Header movido aquí
    ob_end_clean();

    if ($tipoUsuario !== 'cliente') {
        echo json_encode(['success' => false, 'message' => 'Acción no permitida.']);
        exit();
    }

    $reservaId = $_POST['reservaId'] ?? null;
    if (!$reservaId) {
        echo json_encode(['success' => false, 'message' => 'ID de reserva no proporcionado.']);
        exit();
    }

    $resultado = $reservaBusiness->cancelarReservaLibre($reservaId, $usuarioId);

    if ($resultado === true) {
        echo json_encode(['success' => true, 'message' => 'Reserva cancelada con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => is_string($resultado) ? $resultado : 'Error al cancelar la reserva.']);
    }
    exit();
}

// Bloque de fallback
ob_end_clean();
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Acción no reconocida o datos insuficientes.']);