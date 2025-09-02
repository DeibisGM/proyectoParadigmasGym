<?php
session_start();
include '../business/clienteBusiness.php';
include_once '../utility/ImageManager.php';

$clienteBusiness = new ClienteBusiness();
$imageManager = new ImageManager();
$redirect_path = '../view/clienteView.php';

if (isset($_POST['delete_image'])) {
    if (isset($_POST['id'])) {
        $clienteId = $_POST['id'];
        $cliente = $clienteBusiness->getClientePorId($clienteId);
        if ($cliente) {
            $imageManager->deleteImage($cliente->getTbclienteImagenId());
            $cliente->setTbclienteImagenId('');
            $clienteBusiness->actualizarTBCliente($cliente);
            header("location: " . $redirect_path . "?success=image_deleted");
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['insertar'])) {
    if (isset($_POST['carnet']) && isset($_POST['nombre']) && isset($_POST['fechaNacimiento']) &&
        isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['contrasena']) &&
        isset($_POST['direccion']) && isset($_POST['genero']) && isset($_POST['fechaInscripcion'])) {

        $carnet = trim($_POST['carnet']);
        $nombre = trim($_POST['nombre']);
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $contrasena = trim($_POST['contrasena']);
        $direccion = trim($_POST['direccion']);
        $genero = $_POST['genero'];
        $fechaInscripcion = $_POST['fechaInscripcion'];
        $estado = 1; // Por defecto activo

        if (empty($carnet) || empty($nombre) || empty($fechaNacimiento) || empty($telefono) ||
            empty($correo) || empty($contrasena) || empty($direccion) || empty($genero) ||
            empty($fechaInscripcion)) {
            header("location: " . $redirect_path . "?error=datos_faltantes");
            exit();
        }

        if ($clienteBusiness->existeClientePorCarnet($carnet)) {
            header("location: " . $redirect_path . "?error=existe");
            exit();
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            header("location: " . $redirect_path . "?error=correo_invalido");
            exit();
        }

        $cliente = new Cliente(0, $carnet, $nombre, $fechaNacimiento, $telefono, $correo,
            $direccion, $genero, $fechaInscripcion, $estado, $contrasena, '');

        $nuevoId = $clienteBusiness->insertarTBCliente($cliente);

        if ($nuevoId > 0) {
            if (isset($_FILES['tbclienteimagenid']) && !empty($_FILES['tbclienteimagenid']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['tbclienteimagenid'], $nuevoId, 'cli');
                if (!empty($newImageIds)) {
                    $clienteCreado = $clienteBusiness->getClientePorId($nuevoId);
                    $clienteCreado->setTbclienteImagenId($newImageIds[0]);
                    $clienteBusiness->actualizarTBCliente($clienteCreado);
                }
            }
            header("location: " . $redirect_path . "?success=inserted");
        } else {
            header("location: " . $redirect_path . "?error=insertar");
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
} else if (isset($_POST['actualizar'])) {
    if (isset($_POST['id']) && isset($_POST['carnet']) && isset($_POST['nombre']) &&
        isset($_POST['fechaNacimiento']) && isset($_POST['telefono']) && isset($_POST['correo']) &&
        isset($_POST['contrasena']) && isset($_POST['direccion']) && isset($_POST['genero']) &&
        isset($_POST['fechaInscripcion']) && isset($_POST['estado'])) {

        $id = $_POST['id'];
        $clienteActual = $clienteBusiness->getClientePorId($id);

        if ($clienteActual) {
            $clienteActual->setCarnet(trim($_POST['carnet']));
            $clienteActual->setNombre(trim($_POST['nombre']));
            $clienteActual->setFechaNacimiento($_POST['fechaNacimiento']);
            $clienteActual->setTelefono(trim($_POST['telefono']));
            $clienteActual->setCorreo(trim($_POST['correo']));
            $clienteActual->setContrasena(trim($_POST['contrasena']));
            $clienteActual->setDireccion(trim($_POST['direccion']));
            $clienteActual->setGenero($_POST['genero']);
            $clienteActual->setInscripcion($_POST['fechaInscripcion']);
            $clienteActual->setEstado($_POST['estado']);

            if (isset($_FILES['tbclienteimagenid']) && !empty($_FILES['tbclienteimagenid']['name'][0])) {
                if ($clienteActual->getTbclienteImagenId() != '' && $clienteActual->getTbclienteImagenId() != '0'){
                    $imageManager->deleteImage($clienteActual->getTbclienteImagenId());
                }
                $newImageIds = $imageManager->addImages($_FILES['tbclienteimagenid'], $id, 'cli');
                if (!empty($newImageIds)) {
                    $clienteActual->setTbclienteImagenId($newImageIds[0]);
                }
            }

            if ($clienteBusiness->actualizarTBCliente($clienteActual)) {
                header("location: " . $redirect_path . "?success=updated");
            } else {
                header("location: " . $redirect_path . "?error=dbError");
            }
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $result = $clienteBusiness->eliminarTBCliente($id);

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
