<?php
session_start();
header('Content-Type: application/json');

include_once '../business/horarioPersonalBusiness.php';
$horarioPersonalBusiness = new HorarioPersonalBusiness();

$action = $_POST['action'] ?? '';

if ($action === 'reservar_personal') {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $clienteId = $_SESSION['usuario_id'];
    $horarioId = $_POST['horarioId'] ?? null;

    if ($horarioId && $horarioPersonalBusiness->reservarHorarioPersonal($horarioId, $clienteId)) {
        echo json_encode(['success' => true, 'message' => 'Reserva de instructor personal confirmada']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al reservar el horario']);
    }
}
elseif ($action === 'cancelar_personal') {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $clienteId = $_SESSION['usuario_id'];
    $horarioId = $_POST['horarioId'] ?? null;

    if ($horarioId && $horarioPersonalBusiness->cancelarReservaPersonal($horarioId, $clienteId)) {
        echo json_encode(['success' => true, 'message' => 'Reserva cancelada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al cancelar la reserva']);
    }
}
elseif ($action === 'crear_horarios') {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $instructorId = $_POST['instructorId'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $horarios = $_POST['horarios'] ?? [];

    if ($instructorId && $fecha && !empty($horarios)) {
        $slots = [];
        foreach ($horarios as $hora) {
            $slots[] = $fecha . ' ' . $hora;
        }

        $exitos = $horarioPersonalBusiness->crearHorariosPersonales($slots, $instructorId);
        echo json_encode(['success' => true, 'message' => "Se crearon $exitos horarios personales exitosamente"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>