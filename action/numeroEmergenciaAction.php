<?php
include_once '../business/numeroEmergenciaBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

// Insertar un nuevo numero
if (isset($_POST['insertar'])) {
    if (isset($_POST['clienteId']) && isset($_POST['nombre']) && isset($_POST['telefono']) &&
        isset($_POST['relacion'])) {

        Validation::setOldInput($_POST);

        $clienteId = trim($_POST['clienteId']);
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $relacion = trim($_POST['relacion']);

        // Validaciones
        if (empty($clienteId)){
            Validation::setError('clienteId', 'El cliente es obligatorio.');
        }

        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre es obligatorio.');
        } elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        }

        // Verificar si el numero ya existe al cliente
        $numeroEmergenciaBusiness = new numeroEmergenciaBusiness();
        if(empty($telefono)){
            Validation::setError('telefono', 'El numero de telefono es obligatorio.');
        }elseif (!preg_match('/^[428657][0-9]+$/', $telefono)) {
            Validation::setError('telefono', 'El teléfono debe iniciar con 4, 2, 8, 6, 5 o 7.');
        }elseif ($numeroEmergenciaBusiness->existeNumeroEmergencia($clienteId, $telefono)) {
            Validation::setError('telefono', 'El numero ya se encuentra registrado al cliente.');
        }

        if(empty($relacion)) {
            Validation::setError('relacion', 'El tipo de relacion con el cliente es obligatorio.');
        }elseif (preg_match('/[0-9]/', $relacion)) {
            Validation::setError('relacion', 'La relacion no puede contener números.');
        }

        if (Validation::hasErrors()) {
            header("location: ../view/numeroEmergenciaView.php");
            exit();
        }

        // Crear y guardar el numero de emegercia
        $numeroEmergencia = new numeroEmergencia(null, $clienteId, $nombre, $telefono, $relacion);

        $result = $numeroEmergenciaBusiness->insertarTBNumeroEmergencia($numeroEmergencia);

        if ($result == 1) {
            Validation::clear();
            header("location: ../view/numeroEmergenciaView.php?success=insertado");
        } else {
            header("location: ../view/numeroEmergenciaView.php?error=insertar");
        }
    } else {
        header("location: ../view/numeroEmergenciaView.php?error=datos_faltantes");
    }
}
// Actualizar un numero existente
else if (isset($_POST['actualizar'])) {
    if (isset($_POST['id']) && isset($_POST['clienteId']) && isset($_POST['nombre']) &&
        isset($_POST['telefono']) && isset($_POST['relacion'])) {

        Validation::setOldInput($_POST);

        $id = $_POST['id'];
        $clienteId = $_POST['clienteId'];
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $relacion = trim($_POST['relacion']);

        // Validaciones
        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre es obligatorio.');
        } elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        }

        // Verificar si el numero ya existe al cliente
        $numeroEmergenciaBusiness = new numeroEmergenciaBusiness();
        $numeroEmergencia = $numeroEmergenciaBusiness->getNumeroPorId($id);

        if(empty($telefono)){
            Validation::setError('telefono', 'El numero de telefono es obligatorio.');
        }elseif (!preg_match('/^[428657][0-9]+$/', $telefono)) {
            Validation::setError('telefono', 'El teléfono debe iniciar con 4, 2, 8, 6, 5 o 7.');
        }elseif ($numeroEmergencia->getTelefono() != $telefono && $numeroEmergenciaBusiness->existeNumeroEmergencia($clienteId, $telefono)) {
            Validation::setError('telefono', 'El numero ya se encuentra registrado al cliente.');
        }

        if(empty($relacion)) {
            Validation::setError('relacion', 'El tipo de relacion con el cliente es obligatorio.');
        }elseif (preg_match('/[0-9]/', $relacion)) {
            Validation::setError('relacion', 'La relacion no puede contener números.');
        }

        if (Validation::hasErrors()) {
            header("location: ../view/numeroEmergenciaView.php");
            exit();
        }
        // Actualizar el numero de emergencia
        $numeroEmergencia = new numeroEmergencia($id, $clienteId, $nombre, $telefono, $relacion);

        $numeroEmergenciaBusiness = new numeroEmergenciaBusiness();
        $result = $numeroEmergenciaBusiness->actualizarTBNumeroEmergencia($numeroEmergencia);

        if ($result == 1) {

            Validation::clear();
            header("location: ../view/numeroEmergenciaView.php?success=actualizado");
        } else {
            header("location: ../view/numeroEmergenciaView.php?error=actualizar");
        }
    } else {
        header("location: ../view/numeroEmergenciaView.php?error=datos_faltantes");
    }
}

// Eliminar numero de emergencia
else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $numeroEmergenciaBusiness = new numeroEmergenciaBusiness();
        $result = $numeroEmergenciaBusiness->eliminarTBNumeroEmergencia($id);

        if ($result == 1) {
            header("location: ../view/numeroEmergenciaView.php?success=eliminado");
        } else {
            header("location: ../view/numeroEmergenciaView.php?error=eliminar");
        }
    } else {
        header("location: ../view/numeroEmergenciaView.php?error=id_faltante");
    }
} else {
    header("location: ../view/numeroEmergenciaView.php?error=accion_no_valida");
}
?>