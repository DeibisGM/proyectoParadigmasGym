<?php
session_start();
include '../business/clienteBusiness.php';
include '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../utility/Validation.php';

Validation::start();

$instructorBusiness = new InstructorBusiness();
$clienteBusiness = new ClienteBusiness();
$imageManager = new ImageManager();
$redirect_path = 'location:../view/clienteView.php';

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

        Validation::setOldInput($_POST);

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

        if (empty($carnet)){
            Validation::setError('carnet', 'La Identificacion es obligatoria.');
        }elseif ($clienteBusiness->existeClientePorCarnet($carnet)){
            Validation::setError('carnet', 'Identificacion ya registrada.');
        }

        if (empty($nombre)){
            Validation::setError('nombre', 'El nombre es obligatorio.');
        }elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        }

        if (empty($fechaNacimiento)){
            Validation::setError('fechaNacimiento', 'La fecha de nacimiento es obligatoria.');
        }elseif ($fechaNacimiento > date('Y-m-d')) {
            Validation::setError('fechaNacimiento', 'La fecha no puede ser mayor al dia actual.');
        }

        if (empty($telefono)) {
            Validation::setError('telefono', 'El teléfono es obligatorio.');
        } elseif (!preg_match('/^[428657][0-9]+$/', $telefono)) {
            Validation::setError('telefono', 'El teléfono debe iniciar con 4, 2, 8, 6, 5 o 7.');
        }

        if (empty($correo)){
            Validation::setError('correo', 'El correo es obligatorio.');
        }elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Validation::setError('correo', 'El formato del correo no es valido.');
        }elseif ($clienteBusiness->existeclientePorCorreo($correo) || $instructorBusiness->existeInstructorPorCorreo($correo)) {
            Validation::setError('correo', 'El correo ya se encuentra registrado a un usuario.');
        }

        if (empty($contrasena)){
            Validation::setError('contrasena', 'La contraseña es obligatoria.');
        }

        if (empty($direccion)){
            Validation::setError('direccion', 'La direccion en obligatoria.');
        }

        if (empty($genero)){
            Validation::setError('genero', 'El genero es obligatorio.');
        }

        if (empty($fechaInscripcion)){
            Validation::setError('fechaInscripcion', 'El carnet es obligatorio.');
        }elseif ($fechaNacimiento > date('Y-m-d')) {
            Validation::setError('fechaInscripcion', 'La fecha no puede ser mayor al dia actual.');
        }

        if (Validation::hasErrors()) {
            header($redirect_path);
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

            Validation::clear();
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

        Validation::setOldInput($_POST);
        $nombre = trim($_POST['nombre']);
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $contrasena = trim($_POST['contrasena']);
        $direccion = trim($_POST['direccion']);
        $genero = $_POST['genero'];
        $estado = $_POST['estado'];

        if (empty($nombre)){
            Validation::setError('nombre', 'El nombre es obligatorio.');
        }elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        }

        if (empty($fechaNacimiento)){
            Validation::setError('fechaNacimiento', 'La fecha de nacimiento es obligatoria.');
        }elseif ($fechaNacimiento > date('Y-m-d')) {
            Validation::setError('fechaNacimiento', 'La fecha no puede ser mayor al dia actual.');
        }

        if (empty($telefono)) {
            Validation::setError('telefono', 'El teléfono es obligatorio.');
        } elseif (!preg_match('/^[428657][0-9]+$/', $telefono)) {
            Validation::setError('telefono', 'El teléfono debe iniciar con 4, 2, 8, 6, 5 o 7.');
        }


        if (empty($correo)){
            Validation::setError('correo', 'El correo es obligatorio.');
        }elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Validation::setError('correo', 'El formato del correo no es valido.');
        }elseif ($clienteActual->getCorreo() != $correo && $clienteBusiness->existeclientePorCorreo($correo)
            || $instructorBusiness->existeInstructorPorCorreo($correo)) {
            Validation::setError('correo', 'El correo ya se encuentra registrado a un usuario.');
        }

        if (empty($contrasena)){
            Validation::setError('contrasena', 'La contraseña es obligatoria.');
        }

        if (empty($direccion)){
            Validation::setError('direccion', 'La direccion es obligatoria.');
        }

        if (empty($genero)){
            Validation::setError('genero', 'El genero es obligatorio.');
        }

        if (Validation::hasErrors()) {
            header($redirect_path);
            exit();
        }

        if ($clienteActual) {
            $clienteActual->setCarnet(trim($_POST['carnet']));
            $clienteActual->setNombre($nombre);
            $clienteActual->setFechaNacimiento($fechaNacimiento);
            $clienteActual->setTelefono($telefono);
            $clienteActual->setCorreo($correo);
            $clienteActual->setContrasena($contrasena);
            $clienteActual->setDireccion($direccion);
            $clienteActual->setGenero($genero);
            $clienteActual->setInscripcion($_POST['fechaInscripcion']);
            $clienteActual->setEstado($estado);

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
                Validation::clear();
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
