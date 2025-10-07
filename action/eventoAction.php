<?php
include_once '../business/eventoBusiness.php';
include_once '../domain/evento.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['tipo_usuario']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'instructor'])) {
    header("location: ../view/loginView.php?error=unauthorized");
    exit();
}

$eventoBusiness = new EventoBusiness();
$redirect = "location: ../view/eventoGestionView.php";

if (isset($_POST['crear_evento'])) {
    Validation::setOldInput($_POST);

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFin = $_POST['hora_fin'];
    $aforo = $_POST['aforo'];
    $instructorId = $_POST['instructor_id'] ?: null;
    $salas = isset($_POST['salas']) ? $_POST['salas'] : [];
    $tipo = $_POST['tipo']; // NUEVO

    if (empty($nombre)) {
        Validation::setError('nombre', 'El nombre es obligatorio.');
    }
    // NUEVO: Validación de tipo
    if (empty($tipo) || !in_array($tipo, ['abierto', 'privado'])) {
        Validation::setError('tipo', 'Debe seleccionar un tipo de evento válido.');
    }
    if (empty($fecha)) {
        Validation::setError('fecha', 'La fecha es obligatoria.');
    } elseif ($fecha < date('Y-m-d')) {
        Validation::setError('fecha', 'La fecha no puede ser en el pasado.');
    }

    if (empty($horaInicio)) {
        Validation::setError('hora_inicio', 'La hora de inicio es obligatoria.');
    }
    if (empty($horaFin)) {
        Validation::setError('hora_fin', 'La hora de fin es obligatoria.');
    }
    if (!empty($horaInicio) && !empty($horaFin) && $horaInicio >= $horaFin) {
        Validation::setError('hora_fin', 'La hora de fin debe ser posterior a la hora de inicio.');
    }
    if (empty($aforo)) {
        Validation::setError('aforo', 'El aforo es obligatorio.');
    } elseif (!filter_var($aforo, FILTER_VALIDATE_INT) || $aforo <= 0) {
        Validation::setError('aforo', 'El aforo debe ser un número positivo.');
    }
    if (empty($salas)) {
        Validation::setError('salas', 'Debe seleccionar al menos una sala.');
    }

    if (Validation::hasErrors()) {
        header($redirect);
        exit();
    }

    // MODIFICADO: Se añade el tipo al constructor
    $evento = new Evento(0, $instructorId, $tipo, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, 1);
    $resultado = $eventoBusiness->insertarEvento($evento, $salas);

    if ($resultado === true) {
        Validation::clear();
        header($redirect . "?success=event_created");
    } else {
        Validation::setError('general', $resultado);
        header($redirect);
    }

} else if (isset($_POST['update'])) {
    Validation::setOldInput($_POST);
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['horaInicio'];
    $horaFin = $_POST['horaFin'];
    $aforo = $_POST['aforo'];
    $instructorId = $_POST['instructorId'] ?: null;
    $estado = $_POST['estado'];
    $tipo = $_POST['tipo']; // NUEVO

    // Salas no son editables, así que las obtenemos de la BD
    $salas = $eventoBusiness->getSalaIdsPorEvento($id);

    if (empty($nombre)) {
        Validation::setError('nombre_'.$id, 'El nombre es obligatorio.');
    }
    // NUEVO: Validación de tipo
    if (empty($tipo) || !in_array($tipo, ['abierto', 'privado'])) {
        Validation::setError('tipo_'.$id, 'Debe seleccionar un tipo de evento válido.');
    }
    if (empty($fecha)) {
        Validation::setError('fecha_'.$id, 'La fecha es obligatoria.');
    }
    if (empty($horaInicio)) {
        Validation::setError('horaInicio_'.$id, 'La hora de inicio es obligatoria.');
    }
    if (empty($horaFin)) {
        Validation::setError('horaFin_'.$id, 'La hora de fin es obligatoria.');
    }
    if (!empty($horaInicio) && !empty($horaFin) && $horaInicio >= $horaFin) {
        Validation::setError('horaFin_'.$id, 'La hora de fin debe ser posterior a la de inicio.');
    }
    if (empty($aforo)) {
        Validation::setError('aforo_'.$id, 'El aforo es obligatorio.');
    }

    if (Validation::hasErrors()) {
        header($redirect);
        exit();
    }
    // MODIFICADO: Se añade el tipo al constructor
    $evento = new Evento($id, $instructorId, $tipo, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $estado);
    $resultado = $eventoBusiness->actualizarEvento($evento, $salas);

    if ($resultado === true) {
        Validation::clear();
        header($redirect . "?success=event_updated");
    } else {
        Validation::setError('general', $resultado);
        header($redirect);
    }

} else if (isset($_POST['eliminar_evento'])) {
    // Mantener la lógica anterior para delete por ahora
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