<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Error de autenticación.']);
    exit();
}

include_once '../business/reservaBusiness.php';

$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];

if (isset($_POST['action']) && $_POST['action'] === 'create') {
    if ($tipoUsuario !== 'cliente') {
        $response['message'] = 'Solo los clientes pueden reservar.';
    } else {
        $eventoId = $_POST['eventoId'] ?? null;
        $horarioLibreId = $_POST['horarioLibreId'] ?? null;

        $reservaBusiness = new ReservaBusiness();
        $resultado = $reservaBusiness->crearReserva($usuarioId, $eventoId, $horarioLibreId);
        if ($resultado === true) {
            $response = ['success' => true, 'message' => 'Reserva creada con éxito.'];
        } else {
            $response['message'] = is_string($resultado) ? $resultado : 'Error al procesar la reserva.';
        }
    }
}

echo json_encode($response);
?>