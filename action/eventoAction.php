<?php
session_start();
include_once '../business/eventoBusiness.php';
include_once '../domain/evento.php';

if (!isset($_SESSION['tipo_usuario']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'instructor'])) {
    header("location: ../view/loginView.php?error=unauthorized");
    exit();
}

$eventoBusiness = new EventoBusiness();
$redirect = "location: ../view/eventoGestionView.php";

function guardarDatosEnSesion($post_data)
{
    $_SESSION['form_data'] = $post_data;
}

if (isset($_POST['crear_evento'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFin = $_POST['hora_fin'];
    $aforo = $_POST['aforo'];
    $instructorId = $_POST['instructor_id'] ?: null;
    $salas = isset($_POST['salas']) ? $_POST['salas'] : [];

    $_SESSION['form_data'] = $_POST;

    if (empty($nombre) || empty($fecha) || empty($horaInicio) || empty($horaFin) || empty($aforo) || empty($salas)) {
        header($redirect . "?error=" . urlencode("Todos los campos son obligatorios."));
        exit();
    }
    if ($fecha < date('Y-m-d')) {
        header($redirect . "?error=" . urlencode("La fecha del evento no puede ser anterior a hoy."));
        exit();
    }
    if ($horaInicio >= $horaFin) {
        header($redirect . "?error=" . urlencode("La hora de inicio debe ser anterior a la hora de fin."));
        exit();
    }

    $evento = new Evento(0, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $instructorId, 1);
    $resultado = $eventoBusiness->insertarEvento($evento, $salas);

    if ($resultado === true) {
        unset($_SESSION['form_data']);
        header($redirect . "?success=event_created");
    } else {
        header($redirect . "?error=" . urlencode($resultado));
    }

} else if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['horaInicio'];
    $horaFin = $_POST['horaFin'];
    $aforo = $_POST['aforo'];
    $instructorId = $_POST['instructorId'] ?: null;
    $estado = $_POST['estado'];

    // Obtener las salas originales de la base de datos, ya que no son editables.
    $salasStr = $eventoBusiness->getSalaIdsPorEventoId($id);
    if ($salasStr === null) {
        header($redirect . "?error=" . urlencode("Error: No se encontraron las salas originales para este evento."));
        exit();
    }
    $salas = explode('$', $salasStr);

    // Validaciones
    if (empty($id) || empty($nombre) || empty($fecha) || empty($horaInicio) || empty($horaFin) || empty($aforo)) {
        header($redirect . "?error=" . urlencode("Todos los campos son obligatorios al actualizar."));
        exit();
    }
    if ($fecha < date('Y-m-d')) {
        header($redirect . "?error=" . urlencode("La fecha del evento no puede ser anterior a hoy."));
        exit();
    }
    if ($horaInicio >= $horaFin) {
        header($redirect . "?error=" . urlencode("La hora de inicio debe ser anterior a la hora de fin."));
        exit();
    }

    $evento = new Evento($id, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $instructorId, $estado);
    $resultado = $eventoBusiness->actualizarEvento($evento, $salas);

    if ($resultado === true) {
        header($redirect . "?success=event_updated");
    } else {
        header($redirect . "?error=" . urlencode($resultado));
    }

} else if (isset($_POST['eliminar_evento'])) {
    $id = $_POST['id'];
    if (!empty($id)) {
        if ($eventoBusiness->eliminarEvento($id)) {
            header($redirect . "?success=event_deleted");
        } else {
            header($redirect . "?error=" . urlencode("Error en la base de datos al eliminar en cascada."));
        }
    } else {
        header($redirect . "?error=" . urlencode("ID de evento no proporcionado."));
    }

} else {
    header($redirect);
}
?>