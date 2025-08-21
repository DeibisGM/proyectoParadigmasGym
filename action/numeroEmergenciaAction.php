<?php
include_once '../business/numeroEmergenciaBusiness.php';

// Insertar un nuevo numero
if (isset($_POST['insertar'])) {
    if (isset($_POST['clienteId']) && isset($_POST['nombre']) && isset($_POST['telefono']) &&
        isset($_POST['relacion'])) {

        $clienteId = trim($_POST['clienteId']);
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $relacion = trim($_POST['relacion']);

        // Validaciones
        if (empty($clienteId) || empty($nombre) || empty($telefono) ||
            empty($relacion)) {
            header("location: ../view/numeroEmergenciaView.php?error=datos_faltantes");
            exit();
        }

        // Verificar si el numero ya existe al cliente
        $numeroEmergenciaBusiness = new numeroEmergenciaBusiness();
        if ($numeroEmergenciaBusiness->existeNumeroEmergencia($clienteId, $telefono)) {
            header("location: ../view/numeroEmergenciaView.php?error=existe");
            exit();
        }

        // Crear y guardar el numero de emegercia
        $numeroEmergencia = new numeroEmergencia(null, $clienteId, $nombre, $telefono, $relacion);

        $result = $numeroEmergenciaBusiness->insertarTBNumeroEmergencia($numeroEmergencia);

        if ($result == 1) {
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

        $id = $_POST['id'];
        $clienteId = $_POST['clienteId'];
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $relacion = trim($_POST['relacion']);

        // Validaciones
        if (empty($id) || empty($clienteId) || empty($nombre)
            || empty($telefono) || empty($relacion)) {
            header("location: ../view/numeroEmergenciaView.php?error=datos_faltantes");
            exit();
        }

        // Actualizar el numero de emergencia
        $numeroEmergencia = new numeroEmergencia($id, $clienteId, $nombre, $telefono, $relacion);

        $numeroEmergenciaBusiness = new numeroEmergenciaBusiness();
        $result = $numeroEmergenciaBusiness->actualizarTBNumeroEmergencia($numeroEmergencia);

        if ($result == 1) {
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