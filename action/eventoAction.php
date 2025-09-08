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

if (isset($_POST['crear_evento'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFin = $_POST['hora_fin'];
    $aforo = $_POST['aforo'];
    $instructorId = $_POST['instructor_id'] ?: null;
    $salas = isset($_POST['salas']) ? $_POST['salas'] : [];

    if (!empty($nombre) && !empty($fecha) && !empty($horaInicio) && !empty($horaFin) && !empty($aforo) && !empty($salas)) {
        $evento = new Evento(0, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $instructorId, 1);
        
        $resultado = $eventoBusiness->insertarEvento($evento, $salas);

        if ($resultado === true) {
            header($redirect . "?success=event_created");
        } else {
            // Si $resultado es un string, es un mensaje de error
            header($redirect . "?error=" . urlencode($resultado));
        }
    } else {
        header($redirect . "?error=empty_fields");
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
    // Aquí también se necesitaría manejar la actualización de salas

    if (!empty($id) && !empty($nombre) && !empty($fecha) && !empty($horaInicio) && !empty($horaFin) && !empty($aforo)) {
        $evento = new Evento($id, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $instructorId, $estado);
        if ($eventoBusiness->actualizarEvento($evento)) { // Este método también necesitará ser actualizado
            header($redirect . "?success=event_updated");
        } else {
            header($redirect . "?error=db_error");
        }
    } else {
        header($redirect . "?error=empty_fields");
    }

} else if (isset($_POST['eliminar_evento'])) {
    $id = $_POST['id'];
    if (!empty($id)) {
        // Al eliminar un evento, también se deberían limpiar las tablas de relación
        if ($eventoBusiness->eliminarEvento($id)) { // Este método también necesitará ser actualizado
            header($redirect . "?success=event_deleted");
        } else {
            header($redirect . "?error=db_error");
        }
    } else {
        header($redirect . "?error=empty_fields");
    }
} else {
    header($redirect);
}
?>