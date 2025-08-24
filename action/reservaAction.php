<?php
session_start();
header('Content-Type: application/json'); // Esencial para que JavaScript entienda la respuesta

// 1. Verificación de seguridad: el usuario debe haber iniciado sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Error de autenticación: No ha iniciado sesión.']);
    exit();
}

include_once '../business/reservaBusiness.php';

$response = ['success' => false, 'message' => 'Acción no reconocida o datos incorrectos.'];
$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];

// 2. Procesar la acción solicitada (crear o cancelar)
if (isset($_POST['action'])) {
    $reservaBusiness = new ReservaBusiness();

    // Acción para CREAR una nueva reserva
    if ($_POST['action'] === 'create') {
        if ($tipoUsuario !== 'cliente') {
            $response['message'] = 'Solo los clientes pueden realizar reservas.';
        } else {
            $fecha = $_POST['fecha'] ?? null;
            $hora = $_POST['hora'] ?? null;
            // Maneja correctamente si el eventoId es null (para uso libre)
            $eventoId = isset($_POST['eventoId']) && $_POST['eventoId'] !== 'null' ? $_POST['eventoId'] : null;

            if ($fecha && $hora) {
                $resultado = $reservaBusiness->crearReserva($usuarioId, $eventoId, $fecha, $hora);

                if ($resultado === true) {
                    $response['success'] = true;
                    $response['message'] = '¡Reserva creada con éxito!';
                } else {
                    // Si el negocio devuelve un string, es un mensaje de error específico
                    $response['message'] = is_string($resultado) ? $resultado : 'Error desconocido al crear la reserva.';
                }
            } else {
                $response['message'] = 'Faltan datos para procesar la reserva (fecha u hora).';
            }
        }
    } // Acción para CANCELAR una reserva existente
    elseif ($_POST['action'] === 'cancel') {
        $reservaId = $_POST['reservaId'] ?? null;
        if ($reservaId) {
            $resultado = $reservaBusiness->cancelarReserva($reservaId, $usuarioId, $tipoUsuario);

            // La capa de negocio puede devolver true o 1 en caso de éxito
            if ($resultado === true || $resultado == 1) {
                $response['success'] = true;
                $response['message'] = 'Reserva cancelada correctamente.';
            } else {
                $response['message'] = is_string($resultado) ? $resultado : 'Error al cancelar la reserva.';
            }
        } else {
            $response['message'] = 'No se especificó qué reserva cancelar.';
        }
    }
}

// 3. Devolver la respuesta final en formato JSON
echo json_encode($response);
?>