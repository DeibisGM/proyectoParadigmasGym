<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}

include_once '../business/horarioLibreBusiness.php';
$horarioLibreBusiness = new HorarioLibreBusiness();

$response = ['success' => false, 'message' => 'Acción no reconocida.'];

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion === 'crear' && $_SESSION['tipo_usuario'] === 'admin') {
        $slots = $_POST['slots'] ?? [];
        $instructorId = $_POST['instructorId'] ?? null;
        $cupos = $_POST['cupos'] ?? 0;
        $salaId = 1;

        if (!empty($slots) && $instructorId && $cupos > 0) {
            $creados = $horarioLibreBusiness->crearMultiplesHorarios($slots, $salaId, $instructorId, $cupos);
            if ($creados > 0) {
                $response = ['success' => true, 'message' => "Se crearon $creados espacios correctamente."];
            } else {
                $response['message'] = 'No se pudo crear ningún espacio. Es probable que ya existan para la misma hora, fecha y sala.';
            }
        } else {
            $response['message'] = 'Datos incompletos para crear los espacios.';
        }
    } elseif ($accion === 'eliminar' && $_SESSION['tipo_usuario'] === 'admin') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            if ($horarioLibreBusiness->eliminarHorarioLibre($id)) {
                $response = ['success' => true, 'message' => 'Espacio y sus reservas asociadas eliminados correctamente.'];
            } else {
                $response['message'] = 'No se pudo eliminar el espacio.';
            }
        } else {
            $response['message'] = 'ID no proporcionado.';
        }
    }
}

echo json_encode($response);
?>