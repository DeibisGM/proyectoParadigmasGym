<?php
session_start();
include_once '../business/clienteBusiness.php';
include_once '../business/numeroEmergenciaBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: loginView.php");
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];

$clienteBusiness = new ClienteBusiness();
$numeroEmergenciaBusiness = new numeroEmergenciaBusiness();

$redirect_path = '../view/numeroEmergenciaView.php';

if (isset($_POST['insertar'])) {
    if (isset($_POST['clienteId'], $_POST['nombre'], $_POST['telefono'], $_POST['relacion'])) {

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

        if (empty($telefono)) {
            Validation::setError('telefono', 'El numero de telefono es obligatorio.');
        } elseif (!preg_match('/^[428657][0-9]+$/', $telefono)) {
            Validation::setError('telefono', 'El teléfono debe iniciar con 4, 2, 8, 6, 5 o 7.');
        }elseif (!preg_match('/^\d{8}$/', $telefono)) {
            Validation::setError('telefono', 'El numero de telefono tiene que tener 8 digitos.');
        } elseif ($numeroEmergenciaBusiness->existeNumeroEmergencia($clienteId, $telefono)) {
            Validation::setError('telefono', 'El numero ya se encuentra registrado al cliente.');
        }

        if (empty($relacion)) {
            Validation::setError('relacion', 'El tipo de relacion con el cliente es obligatorio.');
        } elseif (preg_match('/[0-9]/', $relacion)) {
            Validation::setError('relacion', 'La relacion no puede contener números.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $numeroEmergencia = new numeroEmergencia(null, $clienteId, $nombre, $telefono, $relacion);
        $result = $numeroEmergenciaBusiness->insertarTBNumeroEmergencia($numeroEmergencia);

        if ($result == 1) {
            Validation::clear();
            header("location: " . $redirect_path . "?success=insertado");
        } else {
            header("location: " . $redirect_path . "?error=insertar");
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
}
else if (isset($_POST['actualizar'])) {
    if (isset($_POST['id'], $_POST['clienteId'], $_POST['nombre'], $_POST['telefono'], $_POST['relacion'])) {

        $id = $_POST['id'];
        $clienteId = $_POST['clienteId'];
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $relacion = trim($_POST['relacion']);

        // Guardar old input por fila
        Validation::setOldInput('nombre_'.$id, $nombre);
        Validation::setOldInput('telefono_'.$id, $telefono);
        Validation::setOldInput('relacion_'.$id, $relacion);

        // Validaciones por fila
        if (empty($nombre)) {
            Validation::setError('nombre_'.$id, 'El nombre es obligatorio.');
        } elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre_'.$id, 'El nombre no puede contener números.');
        }

        $numeroEmergencia = $numeroEmergenciaBusiness->getNumeroPorId($id);

        if (empty($telefono)) {
            Validation::setError('telefono_'.$id, 'El numero de telefono es obligatorio.');
        } elseif (!preg_match('/^[428657][0-9]+$/', $telefono)) {
            Validation::setError('telefono_'.$id, 'El teléfono debe iniciar con 4, 2, 8, 6, 5 o 7.');
        }elseif (!preg_match('/^\d{8}$/', $telefono)) {
            Validation::setError('telefono_'.$id, 'El numero de telefono tiene que tener 8 digitos.');
        }elseif ($numeroEmergencia->getTelefono() != $telefono && $numeroEmergenciaBusiness->existeNumeroEmergencia($clienteId, $telefono)) {
            Validation::setError('telefono_'.$id, 'El numero ya se encuentra registrado al cliente.');
        }

        if (empty($relacion)) {
            Validation::setError('relacion_'.$id, 'El tipo de relacion con el cliente es obligatorio.');
        } elseif (preg_match('/[0-9]/', $relacion)) {
            Validation::setError('relacion_'.$id, 'La relacion no puede contener números.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $numeroEmergenciaActual = new numeroEmergencia($id, $clienteId, $nombre, $telefono, $relacion);
        $result = $numeroEmergenciaBusiness->actualizarTBNumeroEmergencia($numeroEmergenciaActual);

        if ($result == 1) {
            Validation::clear();
            header("location: " . $redirect_path . "?success=actualizado");
        } else {
            header("location: " . $redirect_path . "?error=actualizar");
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
}
else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $result = $numeroEmergenciaBusiness->eliminarTBNumeroEmergencia($id);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=eliminado");
        } else {
            header("location: " . $redirect_path . "?error=eliminar");
        }
    } else {
        header("location: " . $redirect_path . "?error=id_faltante");
    }
} else {
    header("location: " . $redirect_path . "?error=accion_no_valida");
}
?>
