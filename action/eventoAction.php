<?php
session_start();
include_once '../business/eventoBusiness.php';
include_once '../domain/evento.php';

// Solo administradores
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("location: ../view/reservaView.php?error=unauthorized");
    exit();
}

$eventoBusiness = new EventoBusiness();
$redirect = "location: ../view/reservaView.php";

if (isset($_POST['create'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    // CAMBIO: De 'diaSemana' a 'fecha'
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['horaInicio'];
    $horaFin = $_POST['horaFin'];
    $aforo = $_POST['aforo'];
    $instructorId = $_POST['instructorId'] ?: null;

    if (!empty($nombre) && !empty($fecha) && !empty($horaInicio) && !empty($horaFin) && !empty($aforo)) {
        // CAMBIO: Constructor actualizado
        $evento = new Evento(0, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $instructorId, 1);
        if ($eventoBusiness->insertarEvento($evento)) {
            header($redirect . "?success=event_created");
        } else {
            header($redirect . "?error=db_error");
        }
    } else {
        header($redirect . "?error=empty_fields");
    }

} else if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    // CAMBIO: De 'diaSemana' a 'fecha'
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['horaInicio'];
    $horaFin = $_POST['horaFin'];
    $aforo = $_POST['aforo'];
    $instructorId = $_POST['instructorId'] ?: null;
    $estado = $_POST['estado'];

    if (!empty($id) && !empty($nombre) && !empty($fecha) && !empty($horaInicio) && !empty($horaFin) && !empty($aforo)) {
        // CAMBIO: Constructor actualizado
        $evento = new Evento($id, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $instructorId, $estado);
        if ($eventoBusiness->actualizarEvento($evento)) {
            header($redirect . "?success=event_updated");
        } else {
            header($redirect . "?error=db_error");
        }
    } else {
        header($redirect . "?error=empty_fields");
    }

} else if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    if (!empty($id)) {
        if ($eventoBusiness->eliminarEvento($id)) {
            header($redirect . "?success=event_deleted");
        } else {
            header($redirect . "?error=db_error");
        }
    } else {
        header($redirect . "?error=empty_fields");
    }
}
?>